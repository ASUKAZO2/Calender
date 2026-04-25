<?php require_once 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Planner PRO - Clean Design</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    
    <style>
        :root { --main-bg: #f8f9fa; --card-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        body { background-color: var(--main-bg); font-family: 'Sarabun', sans-serif; }
        
        /* Navbar ทันสมัย */
        .navbar { background: white !important; border-bottom: 1px solid #eee; }
        .navbar-brand { color: #2d3436 !important; font-weight: 600; }

        /* การ์ดและปฏิทิน */
        .card { border: none; border-radius: 20px; box-shadow: var(--card-shadow); }
        #calendar { background: white; padding: 20px; border-radius: 20px; }

        /* ปรับปรุงกล่องข้อความรายละเอียด */
        .detail-box { 
            background: #fff; 
            padding: 20px; 
            border-radius: 15px; 
            border: 1px solid #f1f1f1; 
            line-height: 1.8; 
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 1.1rem;
            color: #2d3436;
        }

        /* รูปภาพขนาดใหญ่พอดีจอ */
        .img-preview-box { 
            border-radius: 15px; 
            overflow: hidden; 
            margin-bottom: 20px; 
            background: #000;
        }
        .img-preview-box img { 
            width: 100%; 
            max-height: 60vh; 
            object-fit: contain; 
            cursor: zoom-in;
        }

        /* ปุ่มกระดิ่ง */
        .btn-notif { position: relative; background: #f1f3f5; border-radius: 12px; padding: 8px 12px; cursor: pointer; }
        .badge-dot { position: absolute; top: 5px; right: 5px; width: 10px; height: 10px; background: #ff7675; border-radius: 50%; display: none; }

        /* สีหมวดหมู่ */
        .color-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        
        @media (max-width: 768px) { .display-none-mobile { display: none; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">📅 My Planner <span class="text-primary">PRO</span></a>
            <div class="d-flex align-items-center gap-3">
                <div class="btn-notif" onclick="showTodayTasks()">
                    🔔 <span class="badge-dot" id="notifBadge"></span>
                </div>
                <div class="input-group d-none d-md-flex">
                    <input type="text" id="searchInput" class="form-control border-end-0" style="border-radius: 10px 0 0 10px;" placeholder="ค้นหางานของคุณ...">
                    <button class="btn btn-primary" style="border-radius: 0 10px 10px 0;" onclick="searchEvents()">ค้นหา</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="card p-4 mb-4">
                    <h5 class="fw-bold mb-4 text-dark">✨ บันทึกใหม่</h5>
                    <form action="save_data.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="text" class="form-control bg-light border-0 py-2" name="title" placeholder="หัวข้อเรื่อง" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control bg-light border-0" name="content" rows="3" placeholder="รายละเอียด..."></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-7"><input type="date" class="form-control bg-light border-0" name="event_date" required></div>
                                <div class="col-5"><input type="time" class="form-control bg-light border-0" name="event_time" value="09:00"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">ความสำคัญ:</label>
                            <select class="form-select bg-light border-0" name="event_color">
                                <option value="#0d6efd">🔵 ปกติ (น้ำเงิน)</option>
                                <option value="#ff7675">🔴 ด่วนมาก (แดง)</option>
                                <option value="#55efc4">🟢 ส่วนตัว (เขียว)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control bg-light border-0" name="attachments[]" multiple>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">บันทึกกิจกรรม</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div id="calendar" class="shadow-sm"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0" style="border-radius: 25px;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h4 class="modal-title fw-bold" id="modalTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="modalFiles"></div>
                    <div class="detail-box shadow-sm" id="modalContent"></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button class="btn btn-light text-danger fw-bold me-auto" id="btnDelete">ลบงานนี้</button>
                    <button class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal">เสร็จสิ้น</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="todayToast" class="toast card border-0" role="alert" data-bs-autohide="false">
            <div class="toast-header bg-white border-0 py-3">
                <strong class="me-auto text-primary">🚀 กิจกรรมวันนี้</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body pt-0 pb-3" id="toastBody"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let calendar, allEvents = [], currentId = null;

    function showEventDetail(id) {
        const ev = allEvents.find(e => e.id == id);
        if(!ev) return;
        currentId = id;
        document.getElementById('modalTitle').innerText = ev.title;
        document.getElementById('modalContent').innerText = ev.content || 'ไม่มีรายละเอียดระบุไว้';
        
        const filesDiv = document.getElementById('modalFiles');
        filesDiv.innerHTML = '';
        if(ev.attachments) {
            ev.attachments.forEach(f => {
                const isImg = ['jpg','jpeg','png','gif','webp'].includes(f.type.toLowerCase());
                if(isImg) {
                    filesDiv.innerHTML += `<div class="img-preview-box shadow-sm"><img src="${f.url}" onclick="window.open('${f.url}')"></div>`;
                } else {
                    filesDiv.innerHTML += `<a href="${f.url}" target="_blank" class="btn btn-outline-secondary w-100 mb-3 py-2 fw-bold">📎 ดาวน์โหลดเอกสาร</a>`;
                }
            });
        }
        new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    function showTodayTasks() {
        const todayStr = new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
        const tasks = allEvents.filter(e => e.start.startsWith(todayStr));
        if(tasks.length > 0) {
            let html = `<div class="list-group list-group-flush">`;
            tasks.forEach(t => {
                const time = t.start.includes(' ') ? t.start.split(' ')[1].substring(0,5) : '--:--';
                html += `<a href="#" onclick="showEventDetail(${t.id})" class="list-group-item list-group-item-action border-0 px-0">
                    <span class="fw-bold text-primary">${time}</span> - ${t.title}
                </a>`;
            });
            html += `</div>`;
            document.getElementById('toastBody').innerHTML = html;
            new bootstrap.Toast(document.getElementById('todayToast')).show();
        } else {
            alert('วันนี้คุณไม่มีงานที่บันทึกไว้ครับ พักผ่อนให้เต็มที่!');
        }
    }

    function searchEvents() {
        const kw = document.getElementById('searchInput').value;
        const url = 'get_events.php?search=' + encodeURIComponent(kw);
        fetch(url).then(res => res.json()).then(data => {
            if(data.length > 0) { calendar.setOption('events', url); calendar.gotoDate(data[0].start); }
            else { alert('ไม่พบรายการที่ค้นหา'); }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            locale: 'th',
            displayEventTime: true,
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
            events: 'get_events.php',
            eventClick: (info) => showEventDetail(info.event.id)
        });
        calendar.render();

        fetch('get_events.php').then(res => res.json()).then(events => {
            allEvents = events;
            const todayStr = new Date().toLocaleDateString('en-CA');
            const todayCount = events.filter(e => e.start.startsWith(todayStr)).length;
            if(todayCount > 0) {
                document.getElementById('notifBadge').style.display = 'block';
                showTodayTasks();
            }
        });

        document.getElementById('btnDelete').onclick = function() {
            if(confirm('ต้องการลบงานนี้ใช่หรือไม่?')) {
                const fd = new FormData();
                fd.append('id', currentId);
                fetch('delete_event.php', {method:'POST', body:fd}).then(() => location.reload());
            }
        };
    });
    </script>
</body>
</html>