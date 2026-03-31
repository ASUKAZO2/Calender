<?php require_once 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Planner PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        #calendar { max-height: 650px; background: white; padding: 15px; border-radius: 12px; }
        .fc-event { cursor: pointer; } 
        
        /* สไตล์สำหรับการพิมพ์ (Print) */
        @media print {
            body { background-color: white; }
            .no-print { display: none !important; } /* ซ่อนแถบด้านซ้ายและเมนูตอนสั่งพิมพ์ */
            .col-lg-8 { width: 100% !important; } /* ขยายปฏิทินให้เต็มกระดาษ */
            .card { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary mb-4 shadow-sm no-print">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1 fw-bold">📝 My Planner PRO</span>
            <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 ค้นหางาน/โน้ต...">
                <button class="btn btn-warning text-dark fw-bold" onclick="searchEvents()" style="white-space: nowrap;">ค้นหา</button>
                <button class="btn btn-light fw-bold" onclick="window.print()" style="white-space: nowrap;">🖨️ พิมพ์</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 no-print">
                <div class="card p-4">
                    <h4 class="mb-4 fw-bold text-primary">เพิ่มบันทึกใหม่</h4>
                    <form id="noteForm" action="save_data.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">หัวข้อ</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">รายละเอียด</label>
                            <textarea class="form-control" name="content" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">วันที่แสดงในปฏิทิน</label>
                            <input type="date" class="form-control" name="event_date" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-danger">แนบไฟล์ (เลือกได้หลายไฟล์)</label>
                            <input class="form-control" type="file" name="attachments[]" multiple>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">💾 บันทึกข้อมูล</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card p-0">
                    <div id="calendar"></div>
                </div>
            </div>
        </div> 
    </div> 

    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white no-print">
            <h5 class="modal-title fw-bold" id="eventModalTitle">รายละเอียด</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="mb-2"><strong>รายละเอียด:</strong> <br><span id="eventModalContent" class="text-muted"></span></p>
            <div id="eventModalAttachment" class="mt-3 text-center"></div>
          </div>
          <div class="modal-footer no-print">
            <button type="button" class="btn btn-danger me-auto" id="btnDeleteEvent">🗑️ ลบ</button>
            <button type="button" class="btn btn-warning" id="btnOpenEdit">✏️ แก้ไข</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold">✏️ แก้ไขข้อมูล</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="editEventId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">หัวข้อ</label>
                    <input type="text" class="form-control" id="editTitle" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">รายละเอียด</label>
                    <textarea class="form-control" id="editContent" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">วันที่</label>
                    <input type="date" class="form-control" id="editDate" required>
                </div>
                
                <hr>
                <div class="mb-2 fw-semibold">ไฟล์แนบเดิม:</div>
                <div id="editExistingAttachments" class="mb-3"></div> <div class="mb-2 fw-semibold text-success">➕ เพิ่มไฟล์ใหม่:</div>
                <input class="form-control" type="file" id="editNewAttachments" multiple>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" id="btnSaveEdit">💾 บันทึกการแก้ไข</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    var calendar; // ประกาศ calendar ให้เป็นตัวแปรระดับ Global เพื่อให้ปุ่มค้นหาเรียกใช้ได้

    function searchEvents() {
        // ใช้ .trim() เพื่อตัดช่องว่างเผื่อผู้ใช้เผลอกด Spacebar
        var keyword = document.getElementById('searchInput').value.trim();

        // --- ฟีเจอร์ที่เพิ่ม: ถ้าช่องค้นหาว่างเปล่า ให้กลับมาแสดงผลทั้งหมด ---
        if (keyword === '') {
            calendar.setOption('events', 'get_events.php'); // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
            calendar.gotoDate(new Date()); // กระโดดกลับมาที่เดือนปัจจุบัน
            return; // จบการทำงานของฟังก์ชันค้นหา
        }
        // -------------------------------------------------------

        var url = 'get_events.php?search=' + encodeURIComponent(keyword);

        // ดึงข้อมูลมาดูก่อนว่าผลลัพธ์แรกอยู่ที่วันที่เท่าไหร่
        fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                // ถ้าเจอข้อมูล ให้สั่งปฏิทินกระโดดไปที่วันที่ของกิจกรรมแรกที่พบ
                calendar.gotoDate(data[0].start);
                
                // อัปเดตเหตุการณ์บนปฏิทินให้แสดงเฉพาะที่ค้นหา
                calendar.setOption('events', url);
            } else {
                // ถ้าไม่เจอข้อมูลเลย
                alert('🔍 ไม่พบกิจกรรมที่ตรงกับคำค้นหาของคุณ');
                
                // เคลียร์ช่องค้นหาและกลับมาโชว์ทั้งหมดอัตโนมัติ
                document.getElementById('searchInput').value = '';
                calendar.setOption('events', 'get_events.php');
                calendar.gotoDate(new Date());
            }
        })
        .catch(error => {
            console.error('Error fetching search results:', error);
        });
    }

    // ฟังก์ชันสำหรับลบไฟล์ทีละรูปในหน้าแก้ไข
    function deleteSingleFile(fileId, elementId) {
        if(confirm('ต้องการลบไฟล์นี้ออกจากระบบทันทีใช่หรือไม่?')) {
            var fd = new FormData();
            fd.append('file_id', fileId);
            fetch('delete_attachment.php', { method: 'POST', body: fd })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'success') {
                    document.getElementById(elementId).remove(); // เอาแถบรูปออกจากจอ
                    calendar.refetchEvents(); // อัปเดตปฏิทิน
                } else { alert(data); }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var currentEventId = null; 
        var currentEventDate = null;
        var currentAttachments = []; // เก็บไฟล์ของ event ที่ถูกคลิก

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'th',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: 'get_events.php',
            eventClick: function(info) {
                currentEventId = info.event.id; 
                currentEventDate = info.event.startStr.split('T')[0];
                currentAttachments = info.event.extendedProps.attachments || [];
                
                document.getElementById('eventModalTitle').innerText = info.event.title;
                document.getElementById('eventModalContent').innerText = info.event.extendedProps.content || 'ไม่มีรายละเอียด';
                
                var attachmentDiv = document.getElementById('eventModalAttachment');
                attachmentDiv.innerHTML = ''; 
                
                if (currentAttachments.length > 0) {
                    var html = '<div class="row g-2">'; 
                    currentAttachments.forEach(function(file) {
                        if (['png', 'jpg', 'jpeg', 'gif', 'webp'].includes(file.type.toLowerCase())) {
                            html += '<div class="col-6"><img src="' + file.url + '" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; height: 150px;"></div>';
                        } else {
                            html += '<div class="col-12"><a href="' + file.url + '" target="_blank" class="btn btn-outline-primary w-100 mb-1">📎 เปิดไฟล์แนบ</a></div>';
                        }
                    });
                    html += '</div>';
                    attachmentDiv.innerHTML = html;
                }

                new bootstrap.Modal(document.getElementById('eventModal')).show();
            }
        });

        calendar.render();

        // 1. กดลบทั้งกิจกรรม
        document.getElementById('btnDeleteEvent').addEventListener('click', function() {
            if (confirm('ลบข้อมูลและไฟล์ทั้งหมด?')) {
                var formData = new FormData();
                formData.append('id', currentEventId);
                fetch('delete_event.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") {
                        calendar.getEventById(currentEventId).remove();
                        bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                    }
                });
            }
        });

        // 2. กดปุ่มแก้ไข -> นำข้อมูลและไฟล์ลงฟอร์ม
        document.getElementById('btnOpenEdit').addEventListener('click', function() {
            bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            
            document.getElementById('editEventId').value = currentEventId;
            document.getElementById('editTitle').value = document.getElementById('eventModalTitle').innerText;
            var oldContent = document.getElementById('eventModalContent').innerText;
            document.getElementById('editContent').value = (oldContent === 'ไม่มีรายละเอียด') ? '' : oldContent;
            document.getElementById('editDate').value = currentEventDate;
            document.getElementById('editNewAttachments').value = ''; // ล้างช่องเลือกไฟล์ใหม่

            // โชว์ไฟล์แนบเดิม พร้อมปุ่มลบทิ้ง
            var editAttDiv = document.getElementById('editExistingAttachments');
            editAttDiv.innerHTML = '';
            if(currentAttachments.length > 0) {
                currentAttachments.forEach(function(file) {
                    var boxId = 'filebox_' + file.id;
                    editAttDiv.innerHTML += `
                        <div class="d-flex align-items-center justify-content-between p-2 mb-1 border rounded bg-light" id="${boxId}">
                            <a href="${file.url}" target="_blank" class="text-truncate" style="max-width: 80%;">📎 แนบไว้แล้ว (เปิดดู)</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSingleFile(${file.id}, '${boxId}')">ลบออก</button>
                        </div>
                    `;
                });
            } else {
                editAttDiv.innerHTML = '<span class="text-muted text-sm">ไม่มีไฟล์แนบเดิม</span>';
            }

            new bootstrap.Modal(document.getElementById('editModal')).show();
        });

        // 3. กดบันทึกการแก้ไข
        document.getElementById('btnSaveEdit').addEventListener('click', function() {
            var formData = new FormData();
            formData.append('id', document.getElementById('editEventId').value);
            formData.append('title', document.getElementById('editTitle').value);
            formData.append('content', document.getElementById('editContent').value);
            formData.append('event_date', document.getElementById('editDate').value);

            // ดึงไฟล์ใหม่ที่ถูกเลือก
            var fileInput = document.getElementById('editNewAttachments');
            for (var i = 0; i < fileInput.files.length; i++) {
                formData.append('new_attachments[]', fileInput.files[i]);
            }

            fetch('update_event.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    calendar.refetchEvents();
                } else { alert('Error: ' + data); }
            });
        });

        // เพิ่มการกด Enter ในช่องค้นหา
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') { searchEvents(); }
        });
    });
    </script>
</body>
</html>