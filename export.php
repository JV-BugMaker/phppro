<?php

//导出数据

try {
    $dsn = "mysql:host=127.0.0.1;dbname=ahs";
    $handle = new PDO($dsn, 'root', 'root');
    $handle->exec('set names utf8');
    $sql_cate = "select id,name from ProductCategory where Active=1";
    $rows = $handle->query($sql_cate);
    $data = [];
    foreach ($rows as $key => $row) {
        $data[$key]['name'] = $row['name'];
        //获取brand
        $sql_brand = "select Name from ProductBrand where Active=1 and Category={$row['id']}";
        $rows_brand = $handle->query($sql_brand);

        $data[$key]['brand'] = [];
        foreach ($rows_brand as $k => $item) {
            $data[$key]['brand'][] = $item['Name'];
        }


        //获取quanlity
        $sql_quanlity = "select quanlity_name from product_quanlity where active=1 and category_id={$row['id']}";
        $rows_quanlity = $handle->query($sql_quanlity);

        $data[$key]['quanlity'] = [];

        foreach ($rows_quanlity as $j => $row_quanlity) {
            $data[$key]['quanlity'][] = $row_quanlity['quanlity_name'];
        }


        //获取基本属性
        $sql_name = "select PricePropertyName.ID as id,PricePropertyName.Alias as alias,Name,Value from PricePropertyName left join PricePropertyValue on PricePropertyValue.PropertyName=PricePropertyName.ID where PricePropertyName.Active=1 and PricePropertyValue.Active=1 and PricePropertyName.IsSkuProperty=1 and PricePropertyName.Category={$row['id']} ";
        $rows_name = $handle->query($sql_name);
        $ids = [];
        $data[$key]['property'] = [];
        foreach ($rows_name as $row_name) {
            if (!in_array($row_name['id'], $ids)) {
                $ids[] = $row_name['id'];
                $data[$key]['property'][$row_name['id']] = [
                    'name' => $row_name['Name'].'('.$row_name['alias'].')',
                    'value' => $row_name['Value']
                ];
                continue;
            }
            $data[$key]['property'][$row_name['id']]['value'] .= ',' . $row_name['Value'];
        }
    }

    //写入excel
    $str = '';
    foreach ($data as $key => $item) {
        $str .= $item['name'] . "\n";
        $str .= "\t品牌：\t" . implode("\t", $item['brand']) . "\n";
        $str .= "\t等级：\t" . implode("\t", $item['quanlity']) . "\n";
        foreach ($item['property'] as $property) {
            $str .= "\t" . $property['name'] . ":\t";
            $str .= $property['value'] . "\n";
        }
    }
    $str = mb_convert_encoding($str, 'gbk', 'utf-8');
    file_put_contents('/Users/JV/Desktop/ahs_property.xls', $str);
} catch (Exception $e) {
    var_dump($e->getMessage());
    die;
}
