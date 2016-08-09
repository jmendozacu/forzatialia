/**
 * Open Biz Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file OPEN-BIZ-LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mageconsult.net/terms-and-conditions
 *
 * @category   Magecon
 * @package    Magecon_ProductVideo
 * @version    1.0.0
 * @copyright  Copyright (c) 2012 Open Biz Ltd (http://www.mageconsult.net)
 * @license    http://mageconsult.net/terms-and-conditions
 */
function addRow() {
    var table = document.getElementById("video_table");
 
    var no_videos = document.getElementById("no_videos_available");
    if (no_videos != null){
        document.getElementById("no_videos_available").style.display = 'none';
    }
 
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
 
    var cell1 = row.insertCell(0);
    var element1 = document.createElement("label");
    element1.innerHTML = "No image";
    cell1.appendChild(element1);
 
    var cell2 = row.insertCell(1);
    var element2 = document.createElement("input");
    element2.type = "text";
    element2.name = "key[]";
    cell2.appendChild(element2);
	
    var cell3 = row.insertCell(2);
    var element3 = document.createElement("input");
    element3.type = "text";
    element3.name = "sort[]";
    cell3.appendChild(element3);
	
    row.insertCell(3);
    row.insertCell(4);
}