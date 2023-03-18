![build_status](https://github.com/waterloomatt/agency-analytics/actions/workflows/main.yml/badge.svg)

## AgencyAnalytics Web Crawler

You can access the app at https://agency-analytics.mattskelton.ca/

### General Notes

- built using Laravel 10 and PHP 8.1
- crawler is accessible through the web interface or through a console command `php artisan app:crawl `
- used a Laravel Pipeline to do the crawling and parsing
- there is test coverage (unit tests only)
- originally built using an asynchronous queue but decided to go with a synchronous console app for simplicity

### Crawler specific notes

I used my best judgement about what tags to use to include/exclude some elements.

- internal links were determined by matching their src to one of the following,
    - `href*={some_domain_being_parsed}`
    - `href^=/`
    - `href^=./`
    - `href^=../`
    - `href^=#`
- external links are all links on page that are not internal links
- words were determined by looking at text within `<p>, <span>, <div>`. I ignored `<a>` tags
  since https://agencyanalytics.com uses `<span>` within anchors, ex. `<a><span>text</span></a>`
- the crawler will only crawl unique, internal links. You may notice that the crawler will crawl
  both https://agencyanalytics.com and https://agencyanalytics.com/. I chose not to distinguish between those.