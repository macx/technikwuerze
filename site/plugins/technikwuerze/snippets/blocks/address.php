<?php
$isPackstation = $block->addresstype()->value() === 'packstation';
$name = $block->name()->value();
$zip = $block->zip()->value();
$city = $block->city()->value();

if ($isPackstation) {
  $line2 = $block->postnummer()->value();
  $line3 = 'Packstation ' . $block->packstation()->value();
  $country = 'GERMANY'; // Packstation is generally Germany
} else {
  $line2 = $block->company()->value();
  $line3 = $block->street()->value();
  $country = $block->country()->value();
}

$parts = array_filter([$name, $line2, $line3, $zip . ' ' . $city, $country]);
$addressText = implode("\n", $parts);
if (empty(trim($addressText))) {
  $addressText = 'Technikwürze Address';
}

$qrOptions = [
  'color' => '#000000',
  'back' => '#ffffff',
];
$qrSvgCode = qr($addressText, $qrOptions)->toSvg();
?>
<div class="tw-address-label-wrapper">
  <div class="tw-address-label">
    <!-- SVG Background -->
    <svg class="background" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <rect width="100%" height="100%" fill="#ffffff" />
        
        <!-- Header -->
        <rect x="0" y="0" width="100%" height="60" fill="#ffffff" />
        <line x1="0" y1="60" x2="600" y2="60" stroke="#000000" stroke-width="2" />
        <text x="70" y="45" font-family="Arial, Helvetica, sans-serif" font-weight="900" font-size="32" letter-spacing="1.5" fill="#000000">DHL Paket</text>

        <!-- DHL Logo -->
        <g transform="translate(430, 10)">
            <image href="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxMy4wLjIsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDE0OTQ4KSAgLS0+DQo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMSBUaW55Ly9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLXRpbnkuZHRkIj4NCjxzdmcgdmVyc2lvbj0iMS4xIiBiYXNlUHJvZmlsZT0idGlueSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiDQoJIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNTY2LjkyOXB4IiBoZWlnaHQ9IjE3NS4yMzZweCIgdmlld0JveD0iMCAwIDU2Ni45MjkgMTc1LjIzNiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8cG9seWdvbiBmaWxsPSIjRkZDQzAwIiBwb2ludHM9IjU2Ni45MjcsMCA1NjYuOTI3LDEyNS4xMTEgMCwxMjUuMTExIDAsMCA1NjYuOTI3LDAgCSIvPg0KCTxwYXRoIGZpbGw9IiNEMjAwMkUiIGQ9Ik05OS43LDIyLjQ2N0w4MS41ODcsNDcuMDc1YzAsMCw5My45LDAsOTguNzE3LDBjNC45OSwwLDQuOTI0LDEuODk2LDIuNDg1LDUuMg0KCQljLTIuNDc3LDMuMzU0LTYuNjIyLDkuMTg3LTkuMTQ1LDEyLjU5NGMtMS4yOCwxLjcyOS0zLjU5NSw0Ljg3OSw0LjA3Niw0Ljg3OWM4LjExLDAsNDAuMzY5LDAsNDAuMzY5LDBzNi41MDctOC44NTUsMTEuOTYtMTYuMjU3DQoJCWM3LjQyMS0xMC4wNjksMC42NDQtMzEuMDIzLTI1Ljg4My0zMS4wMjNDMTgwLjQyMywyMi40NjcsOTkuNywyMi40NjcsOTkuNywyMi40NjdMOTkuNywyMi40Njd6Ii8+DQoJPHBhdGggZmlsbD0iI0QyMDAyRSIgZD0iTTc0LjI1OCwxMDIuNjM3bDM2LjM4MS00OS40MzJjMCwwLDQwLjMzMSwwLDQ1LjE0NiwwYzQuOTksMCw0LjkyNSwxLjg5NiwyLjQ4Niw1LjINCgkJYy0yLjQ3NywzLjM1NC02LjY5LDkuMTI1LTkuMjEzLDEyLjUzMmMtMS4yODEsMS43MjktMy41OTUsNC44ODQsNC4wNzUsNC44ODRjOC4xMTEsMCw2MC40ODIsMCw2MC40ODIsMA0KCQljLTUuMDMsNi44OTEtMjEuMzQ2LDI2LjgxNi01MC42NDMsMjYuODE2QzEzOS4yMjksMTAyLjYzNyw3NC4yNTgsMTAyLjYzNyw3NC4yNTgsMTAyLjYzN0w3NC4yNTgsMTAyLjYzN3oiLz4NCgk8cGF0aCBmaWxsPSIjRDIwMDJFIiBkPSJNMjgyLjYxMyw3NS44MTRsLTE5LjcyOSwyNi44MjJoLTUyLjA0MmMwLDAsMTkuNzE4LTI2LjgxNiwxOS43MzUtMjYuODE2TDI4Mi42MTMsNzUuODE0TDI4Mi42MTMsNzUuODE0eiINCgkJLz4NCgk8cG9seWdvbiBmaWxsPSIjRDIwMDJFIiBwb2ludHM9IjM2Mi4zMzksNjkuNzQ4IDIzNS4wNDgsNjkuNzQ4IDI2OS44NTcsMjIuNDY3IDMyMS44ODQsMjIuNDY3IDMwMS45MzIsNDkuNTc4IDMyNS4xNTUsNDkuNTc4IA0KCQkzNDUuMTE1LDIyLjQ2NyAzOTcuMTM2LDIyLjQ2NyAzNjIuMzM5LDY5Ljc0OCAJIi8+DQoJPHBhdGggZmlsbD0iI0QyMDAyRSIgZD0iTTM1Ny44NzEsNzUuODIxbC0xOS43MzgsMjYuODE2SDI4Ni4xMWMwLDAsMTkuNzE4LTI2LjgxNiwxOS43MzUtMjYuODE2SDM1Ny44NzFMMzU3Ljg3MSw3NS44MjF6Ii8+DQoJPHBvbHlnb24gZmlsbD0iI0QyMDAyRSIgcG9pbnRzPSIwLDg2LjM5NSA3Ni42NjgsODYuMzk1IDcyLjQ4LDkyLjA5MiAwLDkyLjA5MiAwLDg2LjM5NSAJIi8+DQoJPHBvbHlnb24gZmlsbD0iI0QyMDAyRSIgcG9pbnRzPSIwLDc1LjgyMSA4NC40NTcsNzUuODIxIDgwLjI2MSw4MS41MSAwLDgxLjUxIDAsNzUuODIxIAkiLz4NCgk8cG9seWdvbiBmaWxsPSIjRDIwMDJFIiBwb2ludHM9IjAsOTYuOTc1IDY4Ljg4Myw5Ni45NzUgNjQuNzE1LDEwMi42MzcgMCwxMDIuNjM3IDAsOTYuOTc1IAkiLz4NCgk8cG9seWdvbiBmaWxsPSIjRDIwMDJFIiBwb2ludHM9IjU2Ni45MjksOTIuMDkyIDQ5MC41NTcsOTIuMDkyIDQ5NC43NDcsODYuMzk2IDU2Ni45MjksODYuMzk2IDU2Ni45MjksOTIuMDkyIAkiLz4NCgk8cG9seWdvbiBmaWxsPSIjRDIwMDJFIiBwb2ludHM9IjU2Ni45MjksMTAyLjYzNyA0ODIuNzkyLDEwMi42NDUgNDg2Ljk2LDk2Ljk3NSA1NjYuOTI5LDk2Ljk3NSA1NjYuOTI5LDEwMi42MzcgCSIvPg0KCTxwb2x5Z29uIGZpbGw9IiNEMjAwMkUiIHBvaW50cz0iNTAyLjUyNiw3NS44MjEgNTY2LjkyOSw3NS44MjEgNTY2LjkyOSw4MS41MTMgNDk4LjM0LDgxLjUxOCA1MDIuNTI2LDc1LjgyMSAJIi8+DQoJPHBhdGggZmlsbD0iI0QyMDAyRSIgZD0iTTQ2OS4xODgsMjIuNDY3bC0zNC44MDQsNDcuMjhoLTU1LjEyOGMwLDAsMzQuODEyLTQ3LjI4LDM0LjgyOS00Ny4yOEg0NjkuMTg4TDQ2OS4xODgsMjIuNDY3eiIvPg0KCTxwYXRoIGZpbGw9IiNEMjAwMkUiIGQ9Ik0zNzQuNzk1LDc1LjgyMWMwLDAtMy44MDMsNS4xOTctNS42NSw3LjY5NWMtNi41MzUsOC44MzItMC43NTgsMTkuMTIxLDIwLjU2OCwxOS4xMjENCgkJYzI0LjYyOCwwLDgzLjU1MiwwLDgzLjU1MiwwbDE5LjczNi0yNi44MTZIMzc0Ljc5NUwzNzQuNzk1LDc1LjgyMXoiLz4NCgk8cG9seWdvbiBmaWxsPSIjRkZDQzAwIiBwb2ludHM9IjAsMTQ1LjQ1MSA1NjYuOTI5LDE0NS40NTEgNTY2LjkyOSwxNDguMjg1IDAsMTQ4LjI4NSAwLDE0NS40NTEgCSIvPg0KCTxwb2x5Z29uIGZpbGw9IiNGRkNDMDAiIHBvaW50cz0iMCwxNTguNjc2IDU2Ni45MjksMTU4LjY3NiA1NjYuOTI5LDE2MS41MSAwLDE2MS41MSAwLDE1OC42NzYgCSIvPg0KCTxwb2x5Z29uIGZpbGw9IiNGRkNDMDAiIHBvaW50cz0iMCwxNzEuODk4IDU2Ni45MjksMTcxLjg5OCA1NjYuOTI5LDE3NC43MzIgMCwxNzQuNzMyIDAsMTcxLjg5OCAJIi8+DQoJPHBvbHlnb24gZmlsbD0iI0QyMDAyRSIgcG9pbnRzPSIxOTAuMzcxLDE3NC43MzIgMjA3LjQzMywxNzQuNzMyIDIxMC4xNTYsMTcxLjA0MSAxOTcuNDU5LDE3MS4wNDEgMjA0LjU3OSwxNjEuMzkzIA0KCQkyMTYuMTQyLDE2MS4zOTMgMjE4Ljg2NiwxNTcuNzAxIDIwNy4zMDMsMTU3LjcwMSAyMTMuNjE3LDE0OS4xNDUgMjI2LjMxNSwxNDkuMTQ1IDIyOS4wMzksMTQ1LjQ1MyAyMTEuOTc4LDE0NS40NTMgDQoJCTE5MC4zNzEsMTc0LjczMiAJIi8+DQoJPHBvbHlnb24gZmlsbD0iI0QyMDAyRSIgcG9pbnRzPSIyMTEuMzEsMTc0LjczMiAyMTYuNTQ2LDE3NC43MzIgMjM0LjM5MywxNjIuMzE2IDIzNC4yMTgsMTc0LjczMiAyMzkuNzE2LDE3NC43MzIgMjM5Ljc1NywxNTkuNDIyIA0KCQkyNjAuMzE5LDE0NS40NTMgMjU1LjM0NSwxNDUuNDUzIDIzOC45NzcsMTU2LjY5NSAyMzkuMTU2LDE0NS40NTMgMjMzLjkyLDE0NS40NTMgMjMzLjc2MSwxNTkuNTA2IDIxMS4zMSwxNzQuNzMyIAkiLz4NCgk8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGZpbGw9IiNEMjAwMkUiIGQ9Ik0yNjYuOTI0LDE0OS4xNDVoMy43MDljMi43MDYsMCw1LjcyOSwxLjM0MiwzLjAwNSw1LjAzMw0KCQljLTIuODQ4LDMuODU5LTcuMjExLDUuMTYtOS45MTcsNS4xNmgtNC4zMkwyNjYuOTI0LDE0OS4xNDVMMjY2LjkyNCwxNDkuMTQ1eiBNMjQzLjY3OCwxNzQuNzMyaDQuMzYzbDguNjM2LTExLjcwM2g0LjI3Ng0KCQljNS40OTgsMCwxMi40OC0yLjMwNywxNy4yNzgtOC44MDljNC45MjItNi42NywxLjI3Ny04Ljc2OC00Ljk2Mi04Ljc2OGgtNy45ODVMMjQzLjY3OCwxNzQuNzMyTDI0My42NzgsMTc0LjczMnoiLz4NCgk8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGZpbGw9IiNEMjAwMkUiIGQ9Ik0yNjguODA1LDE3NC43MzJoNC4zNjRsOS41OTYtMTMuMDA0aDIuNjE4YzIuMzEyLDAsMy43NTMsMCwzLjAwMSwyLjg1MmwtMi44MjIsMTAuMTUyDQoJCWg0Ljk3NWwyLjg0NS0xMS42NjJjMC4zNzItMS44MDMsMC4xODItMi40MzItMC44LTIuOTM2bDAuMDYyLTAuMDg0YzMuOTY5LTAuNTg4LDguNTc0LTMuMTA0LDExLjIwNi02LjY3DQoJCWM1LjE2OS03LjAwNCwwLjA0Ny03LjkyOC01Ljk3NS03LjkyOGgtNy40NjJMMjY4LjgwNSwxNzQuNzMyTDI2OC44MDUsMTc0LjczMnogTTI5Mi4wNTIsMTQ5LjE0NWg0LjE4OA0KCQljNC4wMTUsMCw0Ljg3MSwxLjY3OCwyLjkyMSw0LjMyYy0yLjA3NCwyLjgxMS01LjgxNyw0LjU3Mi05Ljk2Myw0LjU3MmgtMy43MDlMMjkyLjA1MiwxNDkuMTQ1TDI5Mi4wNTIsMTQ5LjE0NXoiLz4NCgk8cG9seWdvbiBmaWxsPSIjRDIwMDJFIiBwb2ludHM9IjI5NS4wNTUsMTc0LjczMiAzMTIuMTE2LDE3NC43MzIgMzE0Ljg0LDE3MS4wNDEgMzAyLjE0MywxNzEuMDQxIDMwOS4yNjIsMTYxLjM5MyAzMjAuODI1LDE2MS4zOTMgDQoJCTMyMy41NDksMTU3LjcwMSAzMTEuOTg2LDE1Ny43MDEgMzE4LjMwMSwxNDkuMTQ1IDMzMC45OTgsMTQ5LjE0NSAzMzMuNzIzLDE0NS40NTMgMzE2LjY2MiwxNDUuNDUzIDI5NS4wNTUsMTc0LjczMiAJIi8+DQoJPHBhdGggZmlsbD0iI0QyMDAyRSIgZD0iTTM1NC43MzksMTQ1Ljk5OGMtMS41NjgtMC43MTMtMy43MjEtMS4wNDktNS43MjgtMS4wNDljLTUuNDk4LDAtMTEuODE3LDIuNzctMTUuNjU2LDcuOTcxDQoJCWMtNi43MTcsOS4xMDIsNy43MDUsNy4yOTksMi44NzUsMTMuODQyYy0yLjUzOCwzLjQzOS03LjAyLDQuNzgzLTkuNTA2LDQuNzgzYy0yLjIyNiwwLTQuMjI1LTAuODQtNS4yNzUtMS40MjhsLTMuMzE0LDMuOTAyDQoJCWMxLjU0MiwwLjYyOSwzLjIwNCwxLjIxNyw1LjM0MiwxLjIxN2M2LjEwOCwwLDEyLjk1My0yLjQ3NywxNy41NjUtOC43MjdjNy4xMi05LjY0Ni02LjkzMS04LjM0OC0yLjc4Mi0xMy45NjcNCgkJYzIuMjYtMy4wNjIsNS43NTktMy45MDIsOC4yMDItMy45MDJjMi4yNywwLDMuMjEyLDAuMzc5LDQuODE4LDEuMjE3TDM1NC43MzksMTQ1Ljk5OEwzNTQuNzM5LDE0NS45OTh6Ii8+DQoJPHBhdGggZmlsbD0iI0QyMDAyRSIgZD0iTTM3Ni41NTgsMTQ1Ljk5OGMtMS41NjktMC43MTMtMy43MjEtMS4wNDktNS43MjktMS4wNDljLTUuNDk4LDAtMTEuODE3LDIuNzctMTUuNjU1LDcuOTcxDQoJCWMtNi43MTgsOS4xMDIsNy43MDQsNy4yOTksMi44NzUsMTMuODQyYy0yLjUzOCwzLjQzOS03LjAyLDQuNzgzLTkuNTA3LDQuNzgzYy0yLjIyNiwwLTQuMjI1LTAuODQtNS4yNzQtMS40MjhsLTMuMzE1LDMuOTAyDQoJCWMxLjU0MywwLjYyOSwzLjIwNCwxLjIxNyw1LjM0MiwxLjIxN2M2LjEwOSwwLDEyLjk1My0yLjQ3NywxNy41NjUtOC43MjdjNy4xMi05LjY0Ni02LjkzLTguMzQ4LTIuNzgyLTEzLjk2Nw0KCQljMi4yNi0zLjA2Miw1Ljc1OS0zLjkwMiw4LjIwMi0zLjkwMmMyLjI3LDAsMy4yMTMsMC4zNzksNC44MTgsMS4yMTdMMzc2LjU1OCwxNDUuOTk4TDM3Ni41NTgsMTQ1Ljk5OHoiLz4NCjwvZz4NCjwvc3ZnPg0K" x="0" y="0" width="160" height="45" />
        </g>
        
        <!-- Sender info -->
        <text x="10" y="77" font-family="Arial" font-weight="bold" font-size="12" fill="#000000">Von:</text>
        <text x="45" y="77" font-family="Arial" font-size="12" fill="#000000">Technikwürze Podcast · Podcaststraße 1 · 12345 Podcity · GERMANY</text>
        
        <!-- Separator -->
        <line x1="0" y1="90" x2="600" y2="90" stroke="#000000" stroke-width="1" />
        
        <!-- Recipient bracket marks [ ] -->
        <path d="M430,105 L445,105 L445,120" fill="none" stroke="#666" stroke-width="1" />
        <path d="M445,250 L445,265 L430,265" fill="none" stroke="#666" stroke-width="1" />

        <!-- Vertical Line right side -->
        <line x1="460" y1="90" x2="460" y2="280" stroke="#000000" stroke-width="1" />

        <!-- Recipient Address via foreignObject -->
        <foreignObject x="20" y="100" width="420" height="175">
            <div xmlns="http://www.w3.org/1999/xhtml" class="address-container">
                <strong>An:</strong>
                <span><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($line2): ?><span><?= htmlspecialchars(
  $line2,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
                <?php if ($line3): ?><span><?= htmlspecialchars(
  $line3,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
                <span><?= htmlspecialchars($zip, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars(
   $city,
   ENT_QUOTES,
   'UTF-8',
 ) ?></span>
                <?php if ($country): ?><span><?= htmlspecialchars(
  $country,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
            </div>
        </foreignObject>

        <!-- GoGreen Logo -->
        <g transform="translate(470, 130)">
            <text x="0" y="0" font-family="Arial" font-size="14" font-weight="bold" fill="#000000">GoGreen</text>
            <text x="0" y="12" font-family="Arial" font-size="9" fill="#000000">Wir kompensieren</text>
            <text x="0" y="22" font-family="Arial" font-size="9" fill="#000000">CO2-Emissionen</text>
            <text x="0" y="32" font-family="Arial" font-size="9" fill="#000000">mit DHL</text>
            <g transform="translate(10, 45)">
                <path d="M10,0 L30,0 L35,10 L45,10 L50,45 L-10,45 L-5,10 L5,10 Z" fill="none" stroke="#000" stroke-width="4"/>
                <text x="20" y="32" font-family="Arial" font-size="20" font-weight="900" fill="#000" text-anchor="middle">10+</text>
            </g>
        </g>

        <!-- Bottom separator -->
        <line x1="0" y1="280" x2="600" y2="280" stroke="#000000" stroke-width="2" />
        
        <!-- Barcode section -->
        <text x="10" y="295" font-family="Arial" font-size="12" fill="#000000" font-weight="bold">Leitcode/Routingcode</text>
        
        <!-- QR Code -->
        <svg x="10" y="305" width="85" height="85">
            <?= $qrSvgCode ?>
        </svg>

        <!-- Easter Egg Barcode -->
        <svg x="110" y="305" width="480" height="85" viewBox="0 0 664 145" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path stroke="#000000" stroke-width="4" d="M2 145L2 0M24 145L24 0M50 145L50 0M82 145L82 0M96 145L96 0M112 145L112 0M144 145L144 0M156 145L156 0M244 145L244 0M260 145L260 0M270 145L270 0M288 145L288 0M294 145L294 0M302 145L302 0M320 145L320 0M376 145L376 0M382 145L382 0M390 145L390 0M424 145L424 0M496 145L496 0M556 145L556 0M584 145L584 0M596 145L596 0M618 145L618 0M626 145L626 0M632 145L632 0M640 145L640 0M662 145L662 0" />
            <path stroke="#000000" stroke-width="2" d="M7 145L7 0M13 145L13 0M41 145L41 0M45 145L45 0M57 145L57 0M67 145L67 0M77 145L77 0M89 145L89 0M107 145L107 0M123 145L123 0M127 145L127 0M133 145L133 0M149 145L149 0M167 145L167 0M173 145L173 0M189 145L189 0M193 145L193 0M199 145L199 0M217 145L217 0M221 145L221 0M227 145L227 0M265 145L265 0M277 145L277 0M309 145L309 0M325 145L325 0M331 145L331 0M347 145L347 0M353 145L353 0M369 145L369 0M397 145L397 0M401 145L401 0M419 145L419 0M431 145L431 0M441 145L441 0M447 145L447 0M481 145L481 0M485 145L485 0M491 145L491 0M507 145L507 0M523 145L523 0M529 145L529 0M545 145L545 0M551 145L551 0M563 145L563 0M573 145L573 0M589 145L589 0M603 145L603 0M607 145L607 0M657 145L657 0" />
            <path stroke="#000000" stroke-width="6" d="M31 145L31 0M475 145L475 0M651 145L651 0" />
            <path stroke="#000000" stroke-width="8" d="M180 145L180 0M208 145L208 0M236 145L236 0M252 145L252 0M338 145L338 0M362 145L362 0M410 145L410 0M456 145L456 0M466 145L466 0M514 145L514 0M538 145L538 0" />
        </svg>
    </svg>
  </div>
</div>
