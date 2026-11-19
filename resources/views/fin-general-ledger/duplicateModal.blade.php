@php
    $dupSource = App\Http\Controllers\FinGeneralLedgerController::getReference($finGeneralLedger->txn_type, $finGeneralLedger->txn_series, 3);
    $dupPrefix = preg_replace('/\d+$/', '', $dupSource);
    $dupLines = $finGeneralLedger->ledgerdetails;
    $dupErrors = $errors->duplicate;
@endphp

{{-- Duplicate voucher: kept outside the edit form because forms cannot be nested. --}}
<div class="modal fade" id="duplicateVoucherModal" tabindex="-1" role="dialog" aria-labelledby="duplicateVoucherTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" action="{{ route('fin-general-ledgers.duplicate', $finGeneralLedger->id) }}" id="duplicateVoucherForm" class="modal-content">
            @csrf

            <div class="modal-header bg-success">
                <h5 class="modal-title" id="duplicateVoucherTitle"><i class="icon-copy3 mr-2"></i> Duplicate {{ $dupSource }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                @if($dupErrors->any())
                    <div class="alert alert-danger border-0">
                        @foreach($dupErrors->all() as $er)
                            <div><i class="icon-arrow-right5"></i> {{ $er }}</div>
                        @endforeach
                    </div>
                @endif

                <div id="duplicateUnsavedWarning" class="alert alert-warning border-0" style="display: none;">
                    <i class="icon-warning22 mr-1"></i>
                    You have unsaved changes on this page. Copies are made from the <b>last saved</b> version of {{ $dupSource }}.
                    Click <b>Update</b> first if you want your changes in the copies.
                </div>

                <div class="d-flex justify-content-between bg-light rounded p-2 mb-3">
                    <div>
                        <div class="text-muted small">{{ App\Http\Controllers\FinGeneralLedgerController::getVoucherHeading($finGeneralLedger->txn_type) }}</div>
                        <div class="font-weight-semibold">{{ $dupSource }} &middot; {{ $dupLines->count() }} {{ \Illuminate\Support\Str::plural('line', $dupLines->count()) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-muted small">Amount</div>
                        <div class="font-weight-semibold">{{ number_format($dupLines->sum('debit'), 2) }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="duplicateCopies" class="font-weight-semibold">Number of copies</label>
                    <div class="input-group" style="max-width: 200px;">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-light" data-dup-step="-1" aria-label="Fewer copies"><i class="icon-minus3"></i></button>
                        </div>
                        <input type="number" class="form-control text-center" id="duplicateCopies" name="copies"
                               min="1" max="{{ $maxDuplicates }}" step="1" required
                               value="{{ old('copies', 1) }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-light" data-dup-step="1" aria-label="More copies"><i class="icon-plus3"></i></button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Between 1 and {{ $maxDuplicates }}.</small>
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold d-block">Transaction date</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="duplicateDateOriginal" name="date_mode" value="original"
                               @if(old('date_mode', 'original') === 'original') checked @endif>
                        <label class="custom-control-label" for="duplicateDateOriginal">
                            Same as original ({{ $finGeneralLedger->txn_date ? date('d M Y', strtotime($finGeneralLedger->txn_date)) : '-' }})
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="duplicateDateCustom" name="date_mode" value="custom"
                               @if(old('date_mode') === 'custom') checked @endif>
                        <label class="custom-control-label" for="duplicateDateCustom">Use a different date</label>
                    </div>
                    <input type="date" class="form-control mt-2" id="duplicateTxnDate" name="txn_date" style="max-width: 200px;"
                           value="{{ old('txn_date', $finGeneralLedger->txn_date) }}">
                </div>

                <div class="alert alert-info border-0 mb-0">
                    <div class="font-weight-semibold mb-1" id="duplicatePreview"></div>
                    <ul class="pl-3 mb-0 small">
                        <li>All accounts, details and amounts are copied.</li>
                        <li>Copies start as <b>Prepared</b> and need checking and approval.</li>
                        <li>Cheque, invoice and attachments are not copied.</li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" id="duplicateSubmit">
                    <i class="icon-copy3 mr-1"></i> <span>Create copy</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        var max = {{ (int) $maxDuplicates }};
        var nextSeries = {{ (int) $nextSeries }};
        var prefix = @json($dupPrefix);
        var $modal = $('#duplicateVoucherModal');
        var $copies = $('#duplicateCopies');
        var $date = $('#duplicateTxnDate');
        var $editForm = $('#editVoucherForm');
        var savedState = $editForm.serialize();

        function voucherNo(series) {
            return prefix + String(series).padStart(3, '0');
        }

        function copies() {
            var n = parseInt($copies.val(), 10);
            return isNaN(n) ? 0 : n;
        }

        function refresh() {
            var n = copies();
            var valid = n >= 1 && n <= max;

            $('#duplicatePreview').text(!valid
                ? 'Enter a number between 1 and ' + max + '.'
                : n === 1
                    ? 'Will create 1 voucher: ' + voucherNo(nextSeries)
                    : 'Will create ' + n + ' vouchers: ' + voucherNo(nextSeries) + ' to ' + voucherNo(nextSeries + n - 1));
            $('#duplicateSubmit').prop('disabled', !valid)
                .find('span').text(valid && n > 1 ? 'Create ' + n + ' copies' : 'Create copy');

            var custom = $('#duplicateDateCustom').is(':checked');
            $date.toggle(custom).prop('required', custom);
        }

        $modal.on('click', '[data-dup-step]', function () {
            var n = Math.min(max, Math.max(1, copies() + parseInt($(this).data('dup-step'), 10)));
            $copies.val(n);
            refresh();
        });
        $copies.on('input change', refresh);
        $modal.on('change', 'input[name="date_mode"]', refresh);

        $modal.on('show.bs.modal', function () {
            $('#duplicateUnsavedWarning').toggle($editForm.serialize() !== savedState);
        });
        $modal.on('shown.bs.modal', function () {
            $copies.trigger('focus').trigger('select');
        });

        $('#duplicateVoucherForm').on('submit', function () {
            $('#duplicateSubmit').prop('disabled', true)
                .html('<i class="icon-spinner2 spinner mr-1"></i> Creating...');
        });

        refresh();

        @if($dupErrors->any())
            $modal.modal('show');
        @endif
    });
</script>
