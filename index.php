<?php

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
        * { margin: 0; padding: 0; }
        table tr:nth-child(even) { background-color: #EEE; }
        </style>
    </head>

    <body>
        <div id="root" />
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

