<?php
ini_set('memory_limit', '2048M');
$path = "./history";

$dateList = getListByPath($path);

$focus_YM = '';
$csvData = [];
foreach ($dateList AS $date) {
    if (!in_array($date, ['.', '..'])) {
        $infoList = getListByPath("{$path}/{$date}");

        $YM = date('Ym', strtotime($date));
        if ($focus_YM !== '' && $focus_YM !== $YM) {
            outputCSV($csvData, "./output/{$focus_YM}.csv");
            $csvData = [];
        }
        $focus_YM = $YM;

        foreach ($infoList AS $timeKey) {
            if (in_array($timeKey, ["summary.log"])) {
                continue;
            }
            echo "{$path}/{$date}/$timeKey\n";

            $data = file_get_contents("{$path}/{$date}/$timeKey");
            $data = json_decode($data, true);
            $formatTime = str_replace(".log", "", $timeKey);
            $formatTime = str_replace("_", ":", $formatTime);
            $formatTime = trim($formatTime);

            $formatDateTime = "$date $formatTime ";

            if (!empty($data['info'])){
                $data['info'] = array_map(function($info) use ($formatDateTime){
                    $info['dateTime'] = $formatDateTime;
                    if (isset($info['mappingName']) && is_array($info['mappingName'])) {
                        $mappingName = [];
                        foreach ($info['mappingName'] AS $_mappingName) {
                            if (isset($info['mappingName']) && is_array($_mappingName)) {
                                foreach ($_mappingName AS $__mappingName) {
                                    $mappingName[] = $__mappingName;
                                }
                            } else {
                                $mappingName[] = $_mappingName;
                            }
                        }

                        $info['mappingName'] = implode(",", $mappingName);
                    }
                    return $info;
                }, $data['info']);
                $csvData = array_merge($csvData, $data['info']);
            }
        }
    }
}
outputCSV($csvData, "./output/{$focus_YM}.csv");

function outputCSV($data, $path)    {

    // 打開一個檔案來寫入 CSV 資料
    $csvFile = fopen($path, 'w');

    if (!isset($data[0])) {
        print_r($data);
        exit();
    }

    // 寫入 CSV 標題列
    fputcsv($csvFile, array_keys($data[0]));
    // 逐一寫入資料列
    foreach ($data as $row) {
        fputcsv($csvFile, $row);
    }

    // 關閉檔案
    fclose($csvFile);
}


function getListByPath($path) {
    $list = scandir($path);
    $list = array_values(array_filter($list, function($name){
        return !in_array($name, ['.', '..', '.DS_Store']);
    }));
    return $list;
}
