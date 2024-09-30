<?php
$trackingNumber = $text;

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

// ตรวจสอบ BOM และลบ BOM ออก
if (substr($trackingData, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
    $trackingData = substr($trackingData, 3);
}

// แปลง JSON เป็น array ด้วย json_decode()
$trackingData1 = json_decode($trackingData, true);

    file_put_contents('log.txt', 'API response1: ' . $trackingData1[0]['DocuID'] . PHP_EOL, FILE_APPEND);

$carouselContents = [];
// ดาวน์โหลดไฟล์
foreach ($trackingData1 as $data) {
    $files = explode(", ", $data["Fileupload"]);
    $fileContents = [];
    foreach ($files as $index => $file) {
        if (!empty($file)) { // ตรวจสอบว่าไฟล์ไม่ใช่ค่าว่าง
            $fileContents[] = [
                "type" => "text",
                "text" => "ไฟล์แนบที่ " . ($index + 1),
                "color" => "#0000FF", // กำหนดสีฟ้า
                "decoration" => "underline", // ขีดเส้นใต้
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
    $bubble = [
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
                    "text" => "เลขที่บิล " . ($data['DocuID'] ?: "-"),
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
                            'text' => ($data['Recorder'] ?: "-"),
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
                            'text' => ($data['CustName'] ?: "-"),
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
                            'text' => ($data['Way'] ?: "-"),
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
                            'text' => ($data['Status'] ?: "-"),
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
                            'text' => ($data['Timestamp'] ?: "-"),
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
                            'text' => ($data['DocuID'] ?: "-"),
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
                            'text' => ($data['SONo'] ?: "-"),
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
                            'text' => ($data['CustName'] ?: "-"),
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
                            'text' => ($data['Date'] ?: "-"),
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
                            'text' => ($data['Time_out'] ?: "-"),
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
                            'text' => ($data['Emp'] ?: "-"),
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
                            'text' => ($data['Car'] ?: "-")  . " (" . ($data['Type_car'] ?: "-") . ")" ,
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
                            'text' => ($data['Note'] ?: "-"),
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
                            'text' => ($data['NotetoWH'] ?: "-"),
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
                            'text' => ($data['Remark'] ?: "-"),
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
                                    "text" => "บาร์โค้ด:",
                                    "weight" => "bold",
                                    "size" => "sm",
                                    "wrap" => true
                                ]
                            ]
                        ],
                        [
                            "type" => "text",
                            'text' => ($data['AWB'] ?: "-"),
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
    ];
    // Add each bubble to carousel contents
    $carouselContents[] = $bubble;
}
// Construct the final carousel message
$trackMessage = [
    'type' => 'flex',
    'altText' => 'ข้อมูลการขนส่งโดยเลขที่บิล',
    'contents' => [
        'type' => 'carousel',
        'contents' => $carouselContents
    ]
];
    $message = json_encode($trackMessage, JSON_UNESCAPED_UNICODE);
?>
