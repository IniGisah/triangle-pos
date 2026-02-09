<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form wire:submit="generateReport">
                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('reports::reports.start_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="start_date" type="date" class="form-control" name="start_date">
                                    @error('start_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('reports::reports.end_date') }} <span class="text-danger">*</span></label>
                                    <input wire:model="end_date" type="date" class="form-control" name="end_date">
                                    @error('end_date')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('reports::reports.customer') }}</label>
                                    <select wire:model="customer_id" class="form-control" name="customer_id">
                                        <option value="">{{ __('reports::reports.select_customer') }}</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports::reports.status') }}</label>
                                    <select wire:model="sale_status" class="form-control" name="sale_status">
                                        <option value="">{{ __('reports::reports.select_status') }}</option>
                                        <option value="Pending">{{ __('reports::reports.status_option_pending') }}</option>
                                        <option value="Shipped">{{ __('reports::reports.status_option_shipped') }}</option>
                                        <option value="Completed">{{ __('reports::reports.status_option_completed') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('reports::reports.payment_status') }}</label>
                                    <select wire:model="payment_status" class="form-control" name="payment_status">
                                        <option value="">{{ __('reports::reports.select_payment_status') }}</option>
                                        <option value="Paid">{{ __('reports::reports.payment_status_paid') }}</option>
                                        <option value="Unpaid">{{ __('reports::reports.payment_status_unpaid') }}</option>
                                        <option value="Partial">{{ __('reports::reports.payment_status_partial') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <span wire:target="generateReport" wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <i wire:target="generateReport" wire:loading.remove class="bi bi-shuffle"></i>
                                {{ __('reports::reports.filter_report') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center mb-0">
                        <div wire:loading.flex class="col-12 position-absolute justify-content-center align-items-center" style="top:0;right:0;left:0;bottom:0;background-color: rgba(255,255,255,0.5);z-index: 99;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">{{ __('reports::reports.loading') }}</span>
                            </div>
                        </div>
                        <thead>
                        <tr>
                            <th>{{ __('reports::reports.date') }}</th>
                            <th>{{ __('reports::reports.reference') }}</th>
                            <th>{{ __('reports::reports.customer_col') }}</th>
                            <th>{{ __('reports::reports.status_col') }}</th>
                            <th>{{ __('reports::reports.total') }}</th>
                            <th>{{ __('reports::reports.paid') }}</th>
                            <th>{{ __('reports::reports.due') }}</th>
                            <th>{{ __('reports::reports.payment_status_col') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</td>
                                <td>{{ $sale->reference }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td>
                                    @if ($sale->status == 'Pending')
                                        <span class="badge badge-info">
                                    {{ __('reports::reports.status_badge_pending') }}
                                </span>
                                    @elseif ($sale->status == 'Shipped')
                                        <span class="badge badge-primary">
                                    {{ __('reports::reports.status_badge_shipped') }}
                                </span>
                                    @else
                                        <span class="badge badge-success">
                                    {{ __('reports::reports.status_badge_completed') }}
                                </span>
                                    @endif
                                </td>
                                <td>{{ format_currency($sale->total_amount) }}</td>
                                <td>{{ format_currency($sale->paid_amount) }}</td>
                                <td>{{ format_currency($sale->due_amount) }}</td>
                                <td>
                                    @if ($sale->payment_status == 'Partial')
                                        <span class="badge badge-warning">
                                    {{ __('reports::reports.payment_status_badge_partial') }}
                                </span>
                                    @elseif ($sale->payment_status == 'Paid')
                                        <span class="badge badge-success">
                                    {{ __('reports::reports.payment_status_badge_paid') }}
                                </span>
                                    @else
                                        <span class="badge badge-danger">
                                    {{ __('reports::reports.payment_status_badge_unpaid') }}
                                </span>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <span class="text-danger">{{ __('reports::reports.no_sales') }}</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div @class(['mt-3' => $sales->hasPages()])>
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
