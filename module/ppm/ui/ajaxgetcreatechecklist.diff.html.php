<?php
declare(strict_types=1);
namespace zin;

$tabContent = zui::diffHub
(
    set::diffUrl($diffUrl),
    set::enableAnnotations(false),
    set::product(0),
    set::module(''),
    set::repoID($repoID),
    set::ppmID(0),
    set::height('calc(100vh - 286px)'),
    set::labels($lang->pipeline->diffHub->labels)
);
