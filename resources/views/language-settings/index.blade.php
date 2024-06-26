@extends('layouts.app')

@push('styles')

@endpush

@section('content')

    <!-- SETTINGS START -->
    <div class="w-100 d-flex ">

        @include('sections.setting-sidebar')

        <x-setting-card>

            <x-slot name="buttons">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <x-forms.button-primary icon="plus" id="add-language"
                                                class="mb-2 mr-2"> @lang('app.addNew') @lang('app.language')
                        </x-forms.button-primary>
                        <x-forms.button-secondary icon="cog" id="translations"
                                                  class="mb-2 mr-2"> @lang('modules.languageSettings.translate')
                        </x-forms.button-secondary>
                        <x-forms.button-secondary icon="cog" id="autoTranslate"
                                                  class="mb-2"> @lang('modules.languageSettings.autoTranslate')
                        </x-forms.button-secondary>
                    </div>
                </div>
            </x-slot>

            <x-slot name="header">

                <div class="s-b-n-header" id="tabs">

                    <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                        @lang($pageTitle)
                    </h2>
                </div>
            </x-slot>


            <!-- LEAVE SETTING START -->
            <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100">

                <div class="mt-2  alert alert-primary mb-2">

                    <div><strong>Note:</strong>
                        {{__('messages.languageEnabledAlertMessage')}}
                    </div>
                </div>

                <x-table class="table table-sm-responsive">
                    <x-slot name="thead">
                        <th>@lang('app.language') @lang('app.name')</th>
                        <th>@lang('app.language') @lang('app.code')</th>
                        <th>@lang('app.status')</th>
                        <th width="50%" class="text-right">@lang('app.action')</th>
                    </x-slot>

                    @forelse($languages as $language)
                        <tr id="languageRow{{ $language->id }}" @class(['bg-additional-grey' => companyOrGlobalSetting()->locale === $language->language_code]) >
                            <td><span class='flag-icon flag-icon-{{ $language->language_code=='en'?'gb':$language->flag_code }} flag-icon-squared'></span> {{ $language->language_name }}</td>
                            <td>{{ $language->language_code }}</td>
                            <td>
                                @if(companyOrGlobalSetting()->locale !== $language->language_code)
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" @if($language->status == 'enabled') checked
                                               @endif class="custom-control-input change-language-setting"
                                               id="{{ $language->id }}">
                                        <label class="custom-control-label cursor-pointer f-14"
                                               for="{{ $language->id }}"></label>
                                    </div>
                                @else

                                    --
                                @endif

                            </td>
                            @php $appSettingLink = "<a href='".route('app-settings.index')."'>".__('app.menu.appSettings')."</a>" @endphp
                            <td @class(['text-right'=>companyOrGlobalSetting()->locale !== $language->language_code,'text-left' => companyOrGlobalSetting()->locale === $language->language_code])>
                                @if($language->language_code !=='en' && companyOrGlobalSetting()->locale != $language->language_code)
                                    @if (companyOrGlobalSetting()->locale != $language->language_code)
                                        <div class="task_view">
                                            <a href="javascript:;" data-language-id="{{ $language->id }}"
                                               class="edit-language task_view_more d-flex align-items-center justify-content-center disabled">
                                                <i class="fa fa-edit icons mr-2"></i> @lang('app.edit')
                                            </a>
                                        </div>
                                        <div class="task_view mt-1 mt-lg-0 mt-md-0">
                                            <a href="javascript:;" data-language-id="{{ $language->id }}"
                                               class="delete-language task_view_more d-flex align-items-center justify-content-center">
                                                <i class="fa fa-trash icons mr-2"></i> @lang('app.delete')
                                            </a>
                                        </div>

                                    @else
                                        @lang('messages.defaultLanguageCantChange',['appsettings'=> $appSettingLink])
                                    @endif
                                @else
                                    @if (companyOrGlobalSetting()->locale == $language->language_code)
                                        <span class="f-12">@lang('messages.defaultLanguageCantChange',['appsettings' => $appSettingLink])</span>
                                    @else
                                        <span class="f-12">@lang('messages.defaultEnLanguageCantChange')</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-cards.no-record-found-list colspan="4"/>
                    @endforelse

                </x-table>

            </div>
            <!-- LEAVE SETTING END -->

        </x-setting-card>

    </div>
    <!-- SETTINGS END -->

@endsection

@push('scripts')

    <script>

        $('body').on('click', '#translations', function () {
            const url = "{{ url('/translations') }}";

            window.open(url, '_blank');
        });


        $('body').on('click', '#add-language', function () {
            var url = "{{ route('language-settings.create')}}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '#autoTranslate', function () {
            var url = "{{ route('language_settings.auto_translate')}}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('body').on('click', '.edit-language', function () {
            var id = $(this).data('language-id');
            var url = "{{ route('language-settings.edit',':id') }}";
            url = url.replace(':id', id);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('.change-language-setting').change(function () {
            var id = this.id;

            if ($(this).is(':checked'))
                var status = 'enabled';
            else
                var status = 'disabled';

            var url = "{{route('language-settings.update', ':id')}}";
            url = url.replace(':id', id);
            $.easyAjax({
                url: url,
                type: "POST",
                blockUI: true,
                data: {'id': id, 'status': status, '_method': 'PUT', '_token': '{{ csrf_token() }}'}
            })
        });

        $('body').on('click', '.delete-language', function () {
            var id = $(this).data('language-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.deleteField')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {

                    var url = "{{ route('language-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        blockUI: true,
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                $('#languageRow' + id).fadeOut();
                            }
                        }
                    });
                }
            });
        });

    </script>
@endpush
