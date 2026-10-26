// =====================================================================
// FMS General Ledger API — Power Query (M) helpers for Power BI
// =====================================================================
//
// 1. Create two Power BI parameters (Home > Manage Parameters):
//      FmsHost    Text  https://fms.agfltech.com
//      FmsApiKey  Text  <the key from `php artisan api-client:create`>
//    (For incremental refresh also create RangeStart / RangeEnd, type Date/Time.)
//
// 2. Create a blank query named  FmsGetAll  and paste the function below.
//
// 3. Create one query per table (examples at the bottom of this file).
//
// Why this shape:
//  - Web.Contents keeps the host static and passes path/filters through
//    RelativePath / Query, which Power BI Service needs for scheduled refresh
//    (a fully dynamic URL causes "dynamic data source" refresh errors).
//  - Pages are fetched with the opaque cursor until meta.has_more = false.
//  - API errors (401/403/422/429/500) surface as readable Power Query errors
//    carrying the API's request_id.
// ---------------------------------------------------------------------

// ---------- Query: FmsGetAll -----------------------------------------
(path as text, optional query as nullable record) as table =>
let
    BaseQuery = if query = null then [] else query,

    GetPage = (cursor as nullable text) as record =>
        let
            Q = if cursor = null then BaseQuery else Record.AddField(BaseQuery, "cursor", cursor),
            Raw = Web.Contents(FmsHost, [
                RelativePath = "api/v1/gl/" & path,
                Query = Q,
                Headers = [Authorization = "Bearer " & FmsApiKey, Accept = "application/json"],
                ManualStatusHandling = {400, 401, 403, 404, 405, 422, 429, 500, 503},
                Timeout = #duration(0, 0, 5, 0)
            ]),
            Json = Json.Document(Raw),
            Checked =
                if Record.HasFields(Json, "error") then
                    error Error.Record(
                        "FMS API " & Text.From(Json[error][status]) & " " & Json[error][code],
                        Json[error][message] & " (request_id " & Text.From(Json[error][request_id]) & ")",
                        Json[error])
                else Json
        in
            Checked,

    Pages = List.Generate(
        () => GetPage(null),
        each _ <> null,
        each if Record.FieldOrDefault(_[meta], "has_more", false) then GetPage(_[meta][next_cursor]) else null,
        each _[data]
    ),
    Rows = List.Combine(Pages),
    Result = Table.FromRecords(Rows, null, MissingField.UseNull)
in
    Result


// ---------- Query: GL Entries (fact) — with incremental refresh -------
// let
//     Source = FmsGetAll("entries", [
//         date_from = Date.ToText(Date.From(RangeStart), "yyyy-MM-dd"),
//         date_to   = Date.ToText(Date.AddDays(Date.From(RangeEnd), -1), "yyyy-MM-dd"),
//         limit     = "5000"
//     ]),
//     Typed = Table.TransformColumnTypes(Source, {
//         {"entry_id", Int64.Type}, {"voucher_id", Int64.Type}, {"account_id", Int64.Type},
//         {"txn_date", type date}, {"debit", Currency.Type}, {"credit", Currency.Type},
//         {"net_amount", Currency.Type}, {"status_id", Int64.Type}, {"vendor_id", Int64.Type},
//         {"is_account_mapped", type logical},
//         {"created_at", type datetimezone}, {"updated_at", type datetimezone}
//     })
// in
//     Typed
//
// Incremental refresh policy on GL Entries: store 6 years, refresh last 3 months.
// Partitions are re-queried as a whole, which also picks up edited and
// deleted vouchers (the FMS hard-deletes and re-creates voucher lines).


// ---------- Query: GL Accounts (dimension) ----------------------------
// let
//     Source = FmsGetAll("accounts", [limit = "5000"]),
//     Typed = Table.TransformColumnTypes(Source, {
//         {"account_id", Int64.Type}, {"level_number", Int64.Type},
//         {"is_posting_account", type logical}, {"hierarchy_complete", type logical}
//     })
// in
//     Typed


// ---------- Query: GL Vouchers (header dimension) ---------------------
// let
//     Source = FmsGetAll("vouchers", [date_from = "2021-01-01", limit = "5000"]),
//     Typed = Table.TransformColumnTypes(Source, {
//         {"voucher_id", Int64.Type}, {"txn_date", type date},
//         {"total_debit", Currency.Type}, {"total_credit", Currency.Type},
//         {"is_balanced", type logical}, {"line_count", Int64.Type}
//     })
// in
//     Typed


// ---------- Query: Voucher Types / Statuses (small lookups) -----------
// FmsGetAll("voucher-types")
// FmsGetAll("voucher-statuses")
// FmsGetAll("account-classes")


// ---------- Model relationships ---------------------------------------
//   GL Accounts[account_id]      1 ── *  GL Entries[account_id]
//   GL Vouchers[voucher_id]      1 ── *  GL Entries[voucher_id]
//   Voucher Types[code]          1 ── *  GL Entries[voucher_type]
//   Account Classes[class_code]  1 ── *  GL Accounts[class_code]
//   Date[Date]                   1 ── *  GL Entries[txn_date]
//
// Example measures (balances are debit-positive):
//   Net Amount   = SUM ( 'GL Entries'[net_amount] )
//   Balance      = CALCULATE ( [Net Amount], FILTER ( ALL ( 'Date' ), 'Date'[Date] <= MAX ( 'Date'[Date] ) ) )
//   Signed Bal.  = IF ( SELECTEDVALUE ( 'GL Accounts'[normal_balance] ) = "credit", -[Balance], [Balance] )
