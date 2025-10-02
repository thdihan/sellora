@extends('layouts.app')

@section('title', 'VAT & TAX Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">VAT & TAX Settings</h3>
                    <p class="text-muted mb-0">Configure VAT and TAX rates for order calculations</p>
                </div>
                <div class="card-body">
                    <form id="taxSettingsForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vat_rate">VAT Rate (%)</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="vat_rate" 
                                           name="vat_rate" 
                                           value="{{ $settings['vat_rate'] }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <small class="form-text text-muted">Enter VAT rate as percentage (e.g., 15 for 15%)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_rate">TAX Rate (%)</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="tax_rate" 
                                           name="tax_rate" 
                                           value="{{ $settings['tax_rate'] }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required>
                                    <small class="form-text text-muted">Enter TAX rate as percentage (e.g., 5 for 5%)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> How VAT & TAX Calculations Work:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Client bears VAT/TAX:</strong> Amount is added to the invoice total</li>
                                        <li><strong>Company bears VAT/TAX:</strong> Amount is absorbed by company, invoice stays unchanged</li>
                                        <li><strong>Example:</strong> Base Price = $1000, VAT = 15%, TAX = 5%</li>
                                        <li class="ml-3">- Client bears both: Invoice = $1000 + $150 + $50 = $1200</li>
                                        <li class="ml-3">- Company bears both: Invoice = $1000 (Company absorbs $200)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#taxSettingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            vat_rate: $('#vat_rate').val(),
            tax_rate: $('#tax_rate').val(),
            _token: $('input[name="_token"]').val()
        };
        
        $.ajax({
            url: '{{ route("settings.tax.update") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error('Failed to update settings');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('An error occurred while updating settings');
                }
            }
        });
    });
});

function resetForm() {
    $('#vat_rate').val('15');
    $('#tax_rate').val('5');
}
</script>
@endpush