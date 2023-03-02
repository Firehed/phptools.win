<?php

declare(strict_types=1);

error_reporting(-1);
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (error_reporting() & $severity !== 0) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});
enum Version: string
{
    case v7_0 = '7.0';
    case v7_1 = '7.1';
    case v7_2 = '7.2';
    case v7_3 = '7.3';
    case v7_4 = '7.4';
    case v8_0 = '8.0';
    case v8_1 = '8.1';
    case v8_2 = '8.2';
}

readonly class Feature
{
    public function __construct(
        public Version $version,
        // category
        public string $name,
        public string $rfc,
        public string $docs,
    ) {
    }
}

// header('Content-type: text/plain');

$fh = fopen('matrix.csv', 'r');
// skip headers, skip blanks
$features = [];
while ($row = fgetcsv($fh)) {
    [$name, $versionStr, $category, $rfc, $docs, $notes] = $row;
    if ($version = Version::tryFrom($versionStr)) {
        $features[] = new Feature(
            version: $version,
            name: $name,
            rfc: $rfc,
            docs: $docs,
        );
    }
}

?>
<!doctype HTML>
<html>
    <head>
        <title>PHP Feature Versions</title>
        <style type="text/css">
        * {
          margin: 0;
          padding: 0;
        }
        table thead th {
          padding: 0 0.5em;
        }
        /* zebra-stripe the table */
        table tr:nth-child(even) {
          background-color: #EEE;
        }
        /* center the table */
        #root > table {
            margin: 0 auto;
        }

        /*!
         * "Fork me on GitHub" CSS ribbon v0.2.3 | MIT License
         * https://github.com/simonwhitaker/github-fork-ribbon-css
        */
        .github-fork-ribbon {
          width: 12.1em;
          height: 12.1em;
          position: absolute;
          overflow: hidden;
          top: 0;
          right: 0;
          z-index: 9999;
          pointer-events: none;
          font-size: 13px;
          text-decoration: none;
          text-indent: -999999px;
        }

        .github-fork-ribbon.fixed {
          position: fixed;
        }

        .github-fork-ribbon:hover, .github-fork-ribbon:active {
          background-color: rgba(0, 0, 0, 0.0);
        }

        .github-fork-ribbon:before, .github-fork-ribbon:after {
          /* The right and left classes determine the side we attach our banner to */
          position: absolute;
          display: block;
          width: 15.38em;
          height: 1.54em;

          top: 3.23em;
          right: -3.23em;

          -webkit-box-sizing: content-box;
          -moz-box-sizing: content-box;
          box-sizing: content-box;

          -webkit-transform: rotate(45deg);
          -moz-transform: rotate(45deg);
          -ms-transform: rotate(45deg);
          -o-transform: rotate(45deg);
          transform: rotate(45deg);
        }

        .github-fork-ribbon:before {
          content: "";

          /* Add a bit of padding to give some substance outside the "stitching" */
          padding: .38em 0;

          /* Set the base colour */
          background-color: #a00;

          /* Set a gradient: transparent black at the top to almost-transparent black at the bottom */
          background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0)), to(rgba(0, 0, 0, 0.15)));
          background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15));
          background-image: -moz-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15));
          background-image: -ms-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15));
          background-image: -o-linear-gradient(top, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15));
          background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.15));

          /* Add a drop shadow */
          -webkit-box-shadow: 0 .15em .23em 0 rgba(0, 0, 0, 0.5);
          -moz-box-shadow: 0 .15em .23em 0 rgba(0, 0, 0, 0.5);
          box-shadow: 0 .15em .23em 0 rgba(0, 0, 0, 0.5);

          pointer-events: auto;
        }

        .github-fork-ribbon:after {
          /* Set the text from the data-ribbon attribute */
          content: attr(data-ribbon);

          /* Set the text properties */
          color: #fff;
          font: 700 1em "Helvetica Neue", Helvetica, Arial, sans-serif;
          line-height: 1.54em;
          text-decoration: none;
          text-shadow: 0 -.08em rgba(0, 0, 0, 0.5);
          text-align: center;
          text-indent: 0;

          /* Set the layout properties */
          padding: .15em 0;
          margin: .15em 0;

          /* Add "stitching" effect */
          border-width: .08em 0;
          border-style: dotted;
          border-color: #fff;
          border-color: rgba(255, 255, 255, 0.7);
        }

        /* color */
        .github-fork-ribbon:before {
          background-color: #4F5B93;
        }
        </style>
    </head>

    <body>
        <a class="github-fork-ribbon" href="https://www.github.com/Firehed/phptools.win" data-ribbon="Edit me on GitHub" title="Edit me on GitHub" target="_blank">Edit me on GitHub</a>
        <div id="root"></div>
        <footer>This site is not affiliated with PHP.net or The PHP Group</footer>
        <script type="text/javascript">
const features = <?=json_encode($features)?>
// https://www.php.net/manual/en/migration70.new-features.php
// https://www.php.net/manual/en/migration71.new-features.php
// https://www.php.net/manual/en/migration72.new-features.php
// https://www.php.net/manual/en/migration73.new-features.php
// https://www.php.net/manual/en/migration74.new-features.php
// https://www.php.net/manual/en/migration80.new-features.php
// https://www.php.net/manual/en/migration81.new-features.php
// https://www.php.net/manual/en/migration82.new-features.php

const makeRow = (values, el = 'td') => {
    const tds = values.map(v => {
        const td = document.createElement(el)
        // td.innerHTML = v
        if (typeof v === 'string') {
            td.innerText = v
        } else {
            td.appendChild(v)
        }
        return td
    })
    const tr = document.createElement('tr')
    tds.forEach(td => tr.appendChild(td))
    return tr
}

const versions = Array.from(new Set(features.map(feat => feat.version))).sort()

const root = document.getElementById('root')
const table = document.createElement('table')
root.appendChild(table)

const thead = document.createElement('thead')
thead.appendChild(makeRow(['Name', 'Links', ...versions], 'th'))
table.appendChild(thead)

const tbody = document.createElement('tbody')

features.forEach(feature => {

    // blah
    const versionInfo = versions.map(version => version >= feature.version ? 'Y' : '')

    // TODO: comma separation
    const links = document.createElement('p')
    if (feature.rfc !== '') {
        const link = document.createElement('a')
        link.innerText = 'RFC ↗️'
        link.href = feature.rfc
        link.rel = 'noopener nofollow'
        link.target = '_blank'
        links.appendChild(link)
    }
    if (feature.docs !== '') {
        const link = document.createElement('a')
        link.innerText = 'Docs ↗️'
        link.href = feature.docs
        link.rel = 'noopener nofollow'
        link.target = '_blank'
        links.appendChild(link)
    }

    const row = makeRow([feature.name, links, ...versionInfo])
    tbody.appendChild(row)
})
table.appendChild(tbody)


console.log(versions)
// document.getElementById('data').innerHTML=JSON.stringify(features)
        </script>
    </body>
</html>

