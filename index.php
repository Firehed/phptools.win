<?php

declare(strict_types=1);

require __DIR__ . '/Version.php';
require __DIR__ . '/Feature.php';

error_reporting(-1);
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (error_reporting() & $severity !== 0) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});

$buildVersion = getenv('GITHUB_SHA');
$buildFooter = $buildVersion
    ? "<footer>Build version <a href=\"https://github.com/Firehed/phptools.win/commit/$buildVersion\" target=\"_blank\"><code>$buildVersion</code></a></footer>"
    : '';

$parsed = yaml_parse_file('data.yaml');

$features = array_map(function ($row) {
    return new Feature(
        version: Version::from($row['version']),
        categories: $row['categories'],
        name: $row['name'],
        rfc: $row['rfc'],
        docs: $row['docs'],
    );
}, $parsed);
?>
<!doctype HTML>
<html>
    <head>
        <title>PHP Feature Versions</title>
        <style type="text/css">
        :root {
          --bg: #f7f6f2;
          --text: #000;
          --table-stripe: #eee;
          --php-purple: #7a86b8;
          --blue: #268bd2;
          --violet: #6c71c4;
        }
        @media (prefers-color-scheme: dark) {
          :root {
            --text: #eaeae9;
            --bg: #3b3936;
            --table-stripe: #322e27;
          }
        }
        
        * {
          margin: 0;
          padding: 0;
          font-family: sans-serif;
        }
        a { color: var(--blue); }
        a:visited { color: var(--violet); }
        body {
          background-color: var(--bg);
          color: var(--text);
        }
        table thead {
          position: sticky;
          top: 0;
          background-color: var(--php-purple);
        }
        table thead th {
          padding: 0 0.5em;
        }
        table tbody td {
          padding: 0.15em 0;
        }
        /* zebra-stripe the table */
        table tr:nth-child(even) {
          background-color: var(--table-stripe);
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/base16/solarized-light.min.css" media="screen">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/base16/solarized-dark.min.css" media="screen and (prefers-color-scheme: dark)">
    </head>

    <body>
        <a class="github-fork-ribbon" href="https://www.github.com/Firehed/phptools.win" data-ribbon="Edit me on GitHub" title="Edit me on GitHub" target="_blank">Edit me on GitHub</a>
        <div id="root"></div>
        <footer>This site is not affiliated with PHP.net or The PHP Group</footer>
        <?=$buildFooter?>
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

    feature.docs.forEach(docLink => {
        const link = document.createElement('a')
        link.innerText = 'Docs ↗️'
        link.href = docLink,
        link.rel = 'noopener nofollow'
        link.target = '_blank'
        links.appendChild(link)
    })

    const name = document.createElement('p')
    name.innerHTML = feature.name

    const row = makeRow([name, links, ...versionInfo])
    tbody.appendChild(row)
})
table.appendChild(tbody)


console.log(versions)
// document.getElementById('data').innerHTML=JSON.stringify(features)
        </script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
        <script type="text/javascript" >
        document.addEventListener('DOMContentLoaded', (event) => {
          document.querySelectorAll('code').forEach((el) => {
            hljs.highlightElement(el)
          })
        })
        </script>
    </body>
</html>

