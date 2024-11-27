<?php

declare(strict_types=1);

$start = hrtime(true);

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

        h1, h2 {
            text-align: center;
        }
        h2 {
            margin-block: 1em;
        }
        #root {
            display: flex;
            flex-direction: column;
            align-items: center;
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

<div id="root">
<h1>PHP Features by version</h1>
<h2>Currently supported PHP versions</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Links</th>
<?=implode('', array_map(fn ($v) => "<th>$v->value</th>", Version::CURRENT))?>
        </tr>
    </thead>
    <tbody>
<?php foreach (array_filter($features, fn ($f) => $f->version->isAddedInCurrent()) as $feature): ?>
        <tr>
            <td><?=$feature->name?></td>
            <td><?=$feature->renderLinks()?></td>
            <?php foreach (Version::CURRENT as $version): ?>
                <td><?=$feature->version->isSupportedInVersion($version) ? 'Y' : ''?>
            <?php endforeach; ?>
        </tr>
<?php endforeach; ?>
</tbody>
</table>

<h2>Next release (<?=Version::UPCOMING->value?>)</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Links</th>
        </tr>
    </thead>
    <tbody>
<?php foreach (array_filter($features, fn ($f) => $f->version->isUpcoming()) as $feature): ?>
        <tr>
            <td><?=$feature->name?></td>
            <td><?=$feature->renderLinks()?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>


<h2>Previously-introduced</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Links</th>
            <th>Introduced</th>
        </tr>
    </thead>
    <tbody>
<?php foreach (array_filter($features, fn ($f) => !($f->version->isAddedInCurrent() || $f->version->isUpcoming())) as $feature): ?>
        <tr>
            <td><?=$feature->name?></td>
            <td><?=$feature->renderLinks()?></td>
            <td><?=$feature->version->value?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
</table>
</div>

        <footer>This site is not affiliated with PHP.net or The PHP Group</footer>
        <?=$buildFooter?>
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
<?php
$renderNs = hrtime(true) - $start;
$renderMs = $renderNs / 1_000_000;
echo '<!-- built in ' . round($renderMs, 3) . 'ms -->';

