<?php

$categoryArray = [
    ['id'=>80253, 'title'=>'Компьютеры и ноутбуки', 'parent_id'=>0],
    ['id'=>80004, 'title'=>'Ноутбуки', 'parent_id'=>80253],
    ['id'=>4630100, 'title'=>'Серверное оборудование', 'parent_id'=>80253],
    ['id'=>125754, 'title'=>'Серверы', 'parent_id'=>4630100],
    ['id'=>4630160, 'title'=>'Контроллеры RAID', 'parent_id'=>4630100],
    ['id'=>4627949, 'title'=>'Смартфоны, ТВ и Электроника', 'parent_id'=>0],
    ['id'=>80258, 'title'=>'ТВ, Аудио/Видео, Фото', 'parent_id'=>4627949],
    ['id'=>80015, 'title'=>'Телевизоры и аксессуары', 'parent_id'=>80258],
    ['id'=>165692, 'title'=>'ТВ-тюнеры', 'parent_id'=>80015],
    ['id'=>80257, 'title'=>'Телефоны, наушники, GPS', 'parent_id'=>4627949],
    ['id'=>80003, 'title'=>'Мобильные телефоны', 'parent_id'=>80257],
    ['id'=>80078, 'title'=>'Встраиваемая техника', 'parent_id'=>1],
];
$categoriesCollection = convertToObject($categoryArray);
/* buildTree() и buildCategoriesTree() вызывать поочередно для демонстрации */
//print("<pre>".print_r(buildTree($categoriesCollection),true)."</pre>");
print("<pre>".print_r(buildCategoriesTree($categoriesCollection),true)."</pre>");


function convertToObject($array)
{
    $object = new \stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = convertToObject($value);
        }
        $object->$key = $value;
    }

    return $object;
}

/*
 * Данный способ является удобным,
 * когда требуется исключительно само дерево категорий
 */
function buildTree($categoriesCollection)
{
    $subcategories = array();

    foreach($categoriesCollection as $category) {
        $subcategories[$category->parent_id][] = $category;
    }

    foreach($categoriesCollection as $category) {
        if (isset($subcategories[$category->id])) {
            $category->subcategories = $subcategories[$category->id];
        }
    }
    return $subcategories[0];
}

/*
 * Данный способ является более гибким,
 * с его помощью мы можем создать полный путь к категории,
 * узнать все дочерние категории для родительской (категории
 * второго, тертьего и других уровней вложенности) и наоборот
 */
function buildCategoriesTree($categoriesCollection)
{
    $categories = [];

    foreach ($categoriesCollection as $category) {
        $categories[$category->id] = $category;
    }

    $categoriesTree = new \stdClass();
    $categoriesTree->subcategories = array();

    $pointers = array();
    $pointers[0] = &$categoriesTree;
    $pointers[0]->level = 0;
    $pointers[0]->subcategories = [];

    $finish = false;
    while (!empty($categories) && !$finish) {
        $flag = false;
        foreach ($categories as $k => $category) {
            if (isset($pointers[$category->parent_id])) {
                $pointers[$category->id] = $pointers[$category->parent_id]->subcategories[$category->id] = $category;
                $pointers[$category->id]->level = 1 + $pointers[$category->parent_id]->level;
                unset($categories[$k]);
                $flag = true;
            }
        }
        if (!$flag) {
            $finish = true;
        }
    }
    unset($pointers[0]);

    return $categoriesTree->subcategories;
}
