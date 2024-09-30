<?php
$trackingNumber = 'OSL64120109';

// $trackingJson = '[{"ID":"114799","DocuID":"4100000196","SONo":"-","CustName":"บริษัท แปดริ้วเครื่องเย็นเชียงใหม่","Date":"07-08-2020","Time_out":"09:30 น.","Emp":"SL-CK สุพัฒน์ (เทน) <\/p>SL-DR กิจจา (ปู)085-5311721 <\/p>SL-DS ชาญยุทธ (เอ๋) <\/p>","Car":"ฒธ-437","Type_car":"กระบะ","Way":"S04-นนทบุรีนอก","Note":"ESS20080096","NotetoWH":"","Ramark":"","Status":"ส่งสำเร็จ","Timestamp":"07-08-2020 | 1:15 PM","Recorder":"SL-คลังสินค้าศาลายา"}]';
// $trackingData = json_decode($trackingJson, true);

// เพิ่มการบันทึกค่า trackingNumber ลงในไฟล์ log
file_put_contents('log.txt', 'Received tracking number: ' . $trackingNumber . PHP_EOL, FILE_APPEND);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "http://sangchaistock.dyndns.org:41984/tracking/ajax/LinebotSearchTracking.php?id=" . $trackingNumber,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
));

$trackingData = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// เพิ่มการบันทึกการตอบกลับจาก API ลงในไฟล์ log
file_put_contents('log.txt', 'API response: ' . $trackingData . PHP_EOL, FILE_APPEND);
// $response = json_decode($trackingJson, true);
// ตรวจสอบ BOM และลบ BOM ออก
if (substr($trackingData, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $trackingData = substr($trackingData, 3);
}

// แปลง JSON เป็น array ด้วย json_decode()
$trackingData1 = json_decode($trackingData, true);
print_r($trackingData1);

    file_put_contents('log.txt', 'API response1: ' . $trackingData1[0]['DocuID'] . PHP_EOL, FILE_APPEND);

$files = explode(", ", $trackingData1[0]["Fileupload"]);
$fileContents = [];

// ดาวน์โหลดไฟล์
foreach ($trackingDataArray as $trackingData1) {
    $files = explode(", ", $trackingData1["Fileupload"]);
    $fileContents = [];
    foreach ($files as $index => $file) {
        $fileContents[] = [
            "type" => "text",
            "text" => "ไฟล์แนบที่ " . ($index + 1),
            "size" => "sm",
            "wrap" => true,
            "action" => [
                "type" => "uri",
                "label" => "ดาวน์โหลดไฟล์แนบ",
                "uri" => $file
            ]
        ];
    }
}
    $trackMessage = [
        'type' => 'flex',
        'altText' => 'ข้อมูลการขนส่งโดยเลขที่บิล',
        'contents' => [
            "type" => "bubble",
            "header" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "ข้อมูลการขนส่ง",
                        "weight" => "bold",
                        "size" => "xl",
                        "color" => "#FFFFFF",
                        "align" => "center"
                    ],
                    [
                        "type" => "text",
                        "text" => "เลขที่บิล " . $trackingData1[0]["DocuID"] ?: "-",
                        "weight" => "regular",
                        "size" => "sm",
                        "color" => "#FFFFFF",
                        "align" => "center"
                    ]
                ],
                "backgroundColor" => "#0264A2"
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "md",
                "contents" => [
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "จาก: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Recorder"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "ถึง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["CustName"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "สถานะ: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Status"] ?: "-",
                                "size" => "sm",
                                "color" => "#28A745",
                                "weight" => "bold",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "ส่งสำเร็จ: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Timestamp"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "separator",
                        "margin" => "md",
                        "color" => "#0050FFFF"
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "เลขที่บิล: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["DocuID"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "เลขที่ Sale order: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["SONo"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "ชื่อลูกค้า: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["CustName"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "วันที่ส่ง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Date"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "เวลาส่ง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Time_out"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "separator",
                        "margin" => "md",
                        "color" => "#0050FFFF"
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "พนักงานส่ง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Emp"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "รถขนส่ง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => ($trackingData1[0]["Car"] ?: "-") . " (" . ($trackingData1[0]["Type_car"] ?: "-") . ")" ,
                                "size" => "sm",
                                "wrap" => true
                            ]                        
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "เส้นทาง: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Way"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "horizontal",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "หมายเหตุ: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Note"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "separator",
                        "margin" => "md",
                        "color" => "#0050FFFF"
                    ],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "Note To Warehouse: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["NotetoWH"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => [
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "Remark: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ],
                            [
                                "type" => "text",
                                "text" => $trackingData1[0]["Remark"] ?: "-",
                                "size" => "sm",
                                "wrap" => true
                            ]
                        ]
                    ],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "contents" => array_merge([
                            [
                                "type" => "box",
                                "layout" => "vertical",
                                "width" => "80px",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "ไฟล์แนบ: ",
                                        "weight" => "bold",
                                        "size" => "sm",
                                        "wrap" => true
                                    ]
                                ]
                            ]
                        ], $fileContents)
                    ]                  
                ]
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "Tracking by Sangchai Group",
                        "size" => "sm",
                        "align" => "center",
                        "color" => "#FFFFFF",
                        "wrap" => true
                    ]
                ],
                "backgroundColor" => "#0D4167"
            ]
        ]
            ];

    $message = json_encode($trackMessage, JSON_UNESCAPED_UNICODE);
// } else {
    
//     //echo "ไม่พบข้อมูลการติดตาม";

//     $message = json_encode($trackMessage, JSON_UNESCAPED_UNICODE);
// }
?>
