<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/ru/datatables.php
return [
    'sEmptyTable' => 'В таблице отсутствуют данные',
    'sInfo' => 'Записи с _START_ до _END_ из _TOTAL_ записей',
    'sInfoEmpty' => 'Записи с 0 до 0 из 0 записей',
    'sInfoFiltered' => '(отфильтровано из _MAX_ записей)',
    'sInfoPostFix' => '',
    'sInfoThousands' => ',',
    'sLengthMenu' => '_MENU_ Записей на странице',
    'sLoadingRecords' => 'Загрузка записей...',
    'sProcessing' => 'Подождите...',
    'sSearch' => 'Поиск:',
    'sZeroRecords' => 'Записи отсутствуют.',
    'oPaginate' => [
        'sFirst' => 'Первая',
        'sPrevious' => 'Предыдущая',
        'sNext' => 'Следующая',
        'sLast' => 'Последняя',
    ],
    'oAria' => [
        'sSortAscending' => ': активировать для сортировки столбца по возрастанию',
        'sSortDescending' => ': активировать для сортировки столбца по убыванию',
    ],
];
