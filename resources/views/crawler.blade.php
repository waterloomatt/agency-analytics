@php use App\Enums\CrawlStatus; @endphp
@extends('layouts.app')

@section('title', 'Crawler')

@section('content')
    <div>
        <div class="border-b-2 pb-3">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                AgencyAnalytics - Web Crawler
            </h1>
        </div>

        <div class="md:grid md:grid-cols-12 md:gap-6 mt-6">
            <div class="mt-5 md:col-span-4 md:mt-0">
                <form method="POST">
                    @csrf
                    <div class="shadow-md sm:overflow-hidden sm:rounded-md">

                        <h2 class="ml-3 my-3 text-lg font-bold leading-4 text-gray-900 sm:truncate sm:text-xl sm:tracking-tight">
                            Start Crawl
                        </h2>

                        @if ($errors->has('exception'))
                            <div class="ml-3 font-bold">
                                <ul>
                                    @foreach ($errors->get('exception') as $error)
                                        <li class="flex items-center font-medium tracking-wide text-red-500 text-xs">
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="space-y-6 bg-white py-5 sm:p-3">
                            <div class="grid grid-cols-3 gap-6">
                                <div class="col-span-3 sm:col-span-2">
                                    <label for="url"
                                           class="block text-sm font-medium leading-6 text-gray-900">URL (including
                                        protocol)</label>
                                    <div class="mt-2 flex rounded-md shadow-sm">
                                        <input type="text"
                                               name="url"
                                               id="url"
                                               value="{{ old('url') }}"
                                               class="block w-full flex-1 rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                               placeholder="https://www.example.com">
                                    </div>

                                    @error('url')
                                    <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>


                            </div>

                            <div>
                                <label for="pages"
                                       class="block text-sm font-medium leading-6 text-gray-900">
                                    Pages to crawl (min 1, max 10)
                                </label>
                                <div class="mt-2">
                                    <input type="number"
                                           value="{{ old('pages', 5) }}"
                                           min="1"
                                           max="10"
                                           name="pages"
                                           id="pages"
                                           class="block w-13 flex-1 rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                </div>

                                @error('pages')
                                <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
        {{ $message }}
    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                            <button type="submit"
                                    class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                                Start Crawl
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="mt-5 md:col-span-8 md:mt-0">
                <div class="shadow-md sm:overflow-hidden sm:rounded-md">
                    <h2 class="ml-3 my-3 text-lg font-bold leading-4 text-gray-900 sm:truncate sm:text-xl sm:tracking-tight">
                        History (Last 5 runs)
                    </h2>

                    <table class="min-w-full leading-normal">
                        <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Pages
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Unique Images
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Unique Internal Links
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Unique External Links
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Avg. Page Load
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Avg. Word Count
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Avg. Title Length
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100">
                                Details
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($crawls as $crawl)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex">
                                        <div class="ml-3">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ $crawl->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->pages }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->unique_images }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->unique_internal_links }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->unique_external_links }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->avg_page_load }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->avg_word_count }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $crawl->avg_title_length }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span @class([
                                        'relative',
                                        'inline-block',
                                        'px-3',
                                        'py-1',
                                        'font-semibold',
                                        'text-orange-900' => $crawl->status === CrawlStatus::RUNNING,
                                        'text-green-900' => $crawl->status === CrawlStatus::COMPLETED,
                                        'text-red-900' => $crawl->status === CrawlStatus::ERROR,
                                        'leading-tight'
                                    ])>
                                        <span @class([
                                            'absolute',
                                            'inset-0',
                                             'bg-orange-200' => $crawl->status === CrawlStatus::RUNNING,
                                             'bg-green-200' => $crawl->status === CrawlStatus::COMPLETED,
                                             'bg-red-200' => $crawl->status === CrawlStatus::ERROR,
                                             'opacity-50',
                                             'rounded-full'
                                    ])></span>

                                      <span class="relative">
                                          {{ $crawl->status }}
                                      </span>
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                    @if ($crawl->details->count() > 0)
                                        @include('partials.crawl_details_modal', ['crawl' => $crawl, 'modal_id' => 'modal_'.$crawl->id])
                                        <button type="button"
                                                class="inline-block text-gray-500 hover:text-gray-700"
                                                onclick="toggleModal('modal_{{ $crawl->id }}')">
                                            <svg class="inline-block h-6 w-6 fill-current"
                                                 viewBox="0 0 24 24">
                                                <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm-2 6a2 2 0 104 0 2 2 0 00-4 0z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function toggleModal(modalID) {
            document.getElementById(modalID).classList.toggle("hidden");
            document.getElementById(modalID + "-backdrop").classList.toggle("hidden");
            document.getElementById(modalID).classList.toggle("flex");
            document.getElementById(modalID + "-backdrop").classList.toggle("flex");
        }
    </script>

@endsection