# General Ledger API v1

Read-only REST API exposing the FMS General Ledger to Power BI and other BI tools.

- **Base URL:** `https://<host>/api/v1/gl`
- **Auth:** API key (`Authorization: Bearer <key>`), scope `gl.read`
- **Format:** JSON, UTF-8. Dates are `YYYY-MM-DD`; timestamps are ISO 8601 with offset (`Asia/Karachi`)
- **Postman:** [`postman/`](postman/) (collection and environment templates)
- **Power BI:** [`powerbi/FmsGeneralLedger.pq`](powerbi/FmsGeneralLedger.pq) (paging function and table queries)

## Contents

1. [Deployment](#deployment)
2. [API keys](#api-keys)
3. [Security model](#security-model)
4. [Conventions](#conventions)
5. [Endpoints](#endpoints)
6. [Errors](#errors)
7. [Power BI](#power-bi)
8. [Business rules and data quality](#business-rules-and-data-quality)

---

## Deployment

```bash
php artisan migrate --force          # creates api_clients + adds GL reporting indexes
php artisan config:clear
php artisan api-client:create "Power BI - Finance" --expires=2027-10-31 --ip=<gateway-ip>
```

The index migration is idempotent: it skips any index that already exists.

Optional `.env` settings (defaults shown):

| Variable | Default | Meaning |
|---|---|---|
| `API_REQUIRE_HTTPS` | `true` | Reject plain HTTP. Not enforced when `APP_ENV` is `local` or `testing` |
| `API_RATE_LIMIT_PER_MINUTE` | `120` | Requests per minute per client |
| `API_MAX_FAILED_ATTEMPTS` | `10` | Failed authentications per IP per minute before a 429 |
| `API_ALLOW_QUERY_KEY` | `false` | Accept the key as `?api_key=`. Needed only for the Power BI "Web API" credential type |
| `GL_API_POSTED_STATUSES` | `3` | `voucher_status` values counted as posted (3 = Approved, the same basis as the FMS reports) |
| `GL_API_MAX_REPORT_DAYS` | `3660` | Largest date range the report endpoints accept |

## API keys

| Command | Purpose |
|---|---|
| `php artisan api-client:create "<name>" [--scope=gl.read] [--ip=1.2.3.4] [--ip=10.0.0.0/24] [--expires=Y-m-d]` | Issue a key. It is printed **once** |
| `php artisan api-client:list` | List clients with state, last use and IP. Keys are never shown |
| `php artisan api-client:revoke <id>` | Revoke a key immediately |

Key format: `fms_<prefix>_<secret>`. Only the SHA-256 hash is stored, so a database leak does not expose usable keys.

To rotate a key without downtime: create a new key, update Power BI, confirm the new key appears in `last_used_at`, then revoke the old one.

## Security model

| Control | Implementation |
|---|---|
| Authentication | Per-client API keys, hashed at rest and compared in constant time |
| Authorisation | Scopes per key (`gl.read`); the whole API is read-only and every non-GET request returns 405 |
| Key lifecycle | Expiry date, instant revocation, `last_used_at` and `last_used_ip` tracking |
| Network | Optional per-key IP or CIDR allow-list (IPv4 and IPv6) |
| Brute force | Failed attempts throttled per IP. Unknown, revoked and expired keys all get the same 401 |
| Abuse | Per-client rate limit, reported in `X-RateLimit-*` headers |
| Transport | HTTPS required outside local; HSTS on HTTPS responses |
| Response headers | `Cache-Control: no-store`, `nosniff`, `X-Frame-Options: DENY`, `CSP: default-src 'none'`, `Referrer-Policy: no-referrer` |
| Input | Strict validation; unknown parameters are rejected; every SQL value is a bound parameter; cursors are validated |
| Output | Fixed JSON error envelope with no stack traces, even when `APP_DEBUG=true` |
| Audit | One structured line per request in `storage/logs/api-YYYY-MM-DD.log` (client, IP, path, status, duration, request id), kept 90 days. Keys are never logged |
| Data minimisation | No user names or e-mails, and no voucher attachments; only IDs and accounting fields |

## Conventions

**Success envelope**

```json
{
  "data": [ ... ],
  "meta":  { "count": 1000, "limit": 1000, "has_more": true, "next_cursor": "eyJpZCI6MTAwMH0", "filters": {...}, "generated_at": "..." },
  "links": { "self": "...", "next": "https://.../entries?cursor=eyJpZCI6MTAwMH0&limit=1000" }
}
```

- **Pagination:** cursor-based (keyset on id), so pages stay stable and fast. Follow `links.next`, or pass `meta.next_cursor` as `cursor`, until `has_more` is `false`. Keep the same filters on every page. `limit` defaults to 1000 and can go up to 5000.
- **Amounts:** JSON numbers rounded to 2 decimals. Balances are debit-positive (`debit - credit`). Use `normal_balance` to flip the sign for display.
- **Status filter (`status`):** `posted` (the default, Approved vouchers), `all`, or a comma-separated list of status ids `1-4`.
- **Type filter (`type`):** comma-separated ids or codes: `1`/`BPV`, `2`/`BRV`, `3`/`CPV`, `4`/`CRV`, `5`/`JV`.
- **`updated_since`:** an ISO 8601 timestamp. URL-encode `+` as `%2B`, or use UTC `Z`.

## Endpoints

| Method and path | Grain | Description |
|---|---|---|
| `GET /` | — | Key check, posted statuses, data coverage, data-quality counters |
| `GET /accounts` | account | Chart of accounts flattened to `l1_code … l5_title`. Filters: `level`, `class_code`, `posting_only` |
| `GET /accounts/{id}` | account | One account |
| `GET /accounts/{id}/ledger` | line | Account statement: opening balance, lines with `running_balance`, closing balance. Requires `date_from` and `date_to`. An L1–L4 account includes all of its descendants |
| `GET /voucher-types` | type | BPV, BRV, CPV, CRV, JV |
| `GET /voucher-statuses` | status | Prepared, Checked, Approved, Received, each with `is_posted` |
| `GET /account-classes` | class | Assets … Income, with normal balance and financial statement |
| `GET /vouchers` | voucher | Headers with `voucher_no`, totals and `is_balanced`. Filters: `date_from`, `date_to`, `status`, `type`, `vendor_id`, `updated_since` |
| `GET /vouchers/{id}` | voucher | Header plus `lines[]` (any status; see `is_posted`) |
| `GET /entries` | line | **Main fact table.** Filters: the voucher filters plus `account_id`, `account_code` (prefix), `voucher_id` |
| `GET /reports/trial-balance` | account | Opening, period debit and credit, and closing balance at `level` L1–L5. Requires `date_from` and `date_to`. `include_zero=true` also lists inactive accounts |
| `GET /reports/monthly-balances` | account × month | Debit and credit per account per `YYYY-MM`. Requires `date_from` and `date_to`. Optional `account_code` prefix |

**Entry row**

```json
{
  "entry_id": 52311, "voucher_id": 1834, "voucher_no": "JV512", "voucher_type": "JV",
  "txn_date": "2026-01-31", "status_id": 3, "account_id": 357, "account_code": "01-02-004-002-0009",
  "is_account_mapped": true, "description": "…", "debit": 1500.0, "credit": 0.0, "net_amount": 1500.0,
  "vendor_id": null, "created_at": "2026-02-01T10:12:03+05:00", "updated_at": "2026-02-01T10:12:03+05:00"
}
```

## Errors

```json
{ "error": { "status": 422, "code": "validation_failed", "message": "The request parameters are invalid.",
             "details": { "dateFrom": ["Unknown query parameter \"dateFrom\"."] }, "request_id": "9e9d…" } }
```

| Status | `code` | When |
|---|---|---|
| 401 | `unauthenticated` | Missing, unknown, revoked or expired key |
| 403 | `insufficient_scope`, `ip_not_allowed`, `https_required` | Key lacks the scope, request comes from a non-allowed IP, or plain HTTP was used |
| 404 | `not_found` | Unknown id or path |
| 405 | `method_not_allowed` | Any method other than GET |
| 422 | `validation_failed` | Invalid or unknown parameter, malformed cursor, date range too large |
| 429 | `rate_limited`, `too_many_failed_attempts` | Wait for the time in `Retry-After` |
| 500 | `server_error` | Look up `request_id` in `storage/logs/` |

## Power BI

Use [`powerbi/FmsGeneralLedger.pq`](powerbi/FmsGeneralLedger.pq):

1. Create the parameters `FmsHost` and `FmsApiKey` (and `RangeStart` and `RangeEnd` for incremental refresh).
2. Add the `FmsGetAll` function, then one query per table: Entries, Accounts, Vouchers, and the lookup tables.
3. Set up the relationships and measures listed at the end of the file.
4. Incremental refresh on Entries: filter by `date_from` and `date_to` from `RangeStart` and `RangeEnd`. A partition is always re-queried as a whole, so edited and deleted vouchers are picked up. (FMS hard-deletes vouchers and re-creates their lines on edit, which is why `updated_since` cannot detect deletions.)

**Where the key lives.** With the default (header) mode, Power BI uses the *Anonymous* credential and the key sits in the `FmsApiKey` parameter, which anyone who can edit the dataset can see. For production:

- Refresh through an **on-premises data gateway** and pin the key to the gateway's IP (`--ip=`).
- Give each dataset its own key, set an expiry, and limit who can edit the dataset.
- To keep the key in Power BI's encrypted credential store instead, set `API_ALLOW_QUERY_KEY=true` and call `Web.Contents(FmsHost, [RelativePath = ..., Query = ..., ApiKeyName = "api_key"])` with the *Web API* credential type. The trade-off: the key then appears in web-server access logs. The API strips it from its own logs and links.

## Business rules and data quality

- **Posted:** `voucher_status = 3` (Approved). This is the same basis as the FMS trial balance, P&L and balance sheet reports, so totals reconcile with them.
- **Voucher number:** type code plus series padded to 3 digits (`BPV001`), as printed by the application.
- **Lines without a voucher** are always excluded, as in the web reports.
- `GET /` reports data-quality counters. Lines on accounts that no longer exist come back with `is_account_mapped = false`. Vouchers whose lines do not balance have `is_balanced = false`. In the trial balance, accounts whose parent chain is broken come back as their own row with `rollup_complete = false`, so totals are never silently lost.
