<div class="hidden overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center"
     id="{{ $modal_id }}">
    <div class="relative w-auto my-6 mx-auto max-w-3xl">
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            <div class="flex items-start justify-between p-5 border-b border-solid border-slate-200 rounded-t">
                <h3 class="text-3xl font-semibold">
                    HTTP Status Codes
                </h3>
                <button
                        class="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                        onclick="toggleModal('{{ $modal_id }}')">
                    <span
                            class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                        ×
                    </span>
                </button>
            </div>

            <div class="relative p-6 flex-auto text-left">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <div class="border-t border-gray-200">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                    <tr>
                                        <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            URL
                                        </th>
                                        <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            HTTP Status
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach ($crawl->details as $crawlPage)
                                        <tr>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">{{ $crawlPage->url }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ $crawlPage->http_status }}</p>
                                            </td>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="flex items-center justify-end p-6 border-t border-solid border-slate-200 rounded-b">
                <button
                        class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                        type="button"
                        onclick="toggleModal('{{ $modal_id }}')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black"
     id="{{ $modal_id }}-backdrop"></div>
