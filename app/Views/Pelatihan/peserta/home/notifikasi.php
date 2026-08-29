<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #c62828;
        --accent-yellow: #ffb300;
        --teal-kemenkes: #00bfa5;
        --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .notif-card {
        /* inline replacement with glass-card-global */
    }

    .notif-item {
        padding: 25px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: all 0.2s ease;
        position: relative;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item:hover {
        background-color: rgba(255,255,255,0.05);
    }

    .notif-item.unread {
        background-color: rgba(255,255,255,0.1);
    }

    .notif-item.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #f59e0b; /* warning/gold indicator for unread */
    }

    .notif-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .bg-success-light { background-color: #dcfce7; color: #15803d; }
    .bg-info-light { background-color: #e0f2fe; color: #0369a1; }
    .bg-warning-light { background-color: #fef9c3; color: #854d0e; }
    .bg-danger-light { background-color: #fee2e2; color: #b91c1c; }

</style>

<div class="pt-1 mb-5 glass-wrapper-global">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-white">Notifikasi</h3>
                    <p class="text-white opacity-75 small mb-0">Informasi terbaru mengenai aktivitas pelatihan Anda.</p>
                </div>
                <div class="input-group rounded-pill shadow-sm overflow-hidden" style="max-width: 420px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-white opacity-50"></i></span>
                    <input id="notificationSearch" type="text" class="form-control border-0 bg-transparent text-white placeholder-white" placeholder="Cari notifikasi...">
                    <style>.placeholder-white::placeholder { color: rgba(255,255,255,0.7); }</style>
                </div>
                <button class="btn btn-action-global rounded-pill px-4 fw-bold small text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" onclick="markAllRead()"><i class="fas fa-check-double me-2"></i> Tandai Semua Terbaca</button>
            </div>

            <div class="p-4" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px;">
                <?php if (empty($notifikasi)) : ?>
                    <div class="p-5 text-center">
                        <div class="d-inline-flex p-4 rounded-circle mb-3" style="background: rgba(255,255,255,0.1);">
                            <i class="fas fa-bell-slash fa-3x text-white opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-white">Belum ada notifikasi</h5>
                        <p class="text-white opacity-75 small">Kami akan memberitahu Anda jika ada aktivitas terbaru.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($notifikasi as $n) : ?>
                        <?php 
                            $icon = 'fa-bell';
                            $colorClass = 'bg-info-light';
                            
                            $type = strtolower($n['type'] ?? 'info');
                            if ($type == 'success') {
                                $icon = 'fa-check-circle';
                                $colorClass = 'bg-success-light';
                            } elseif ($type == 'error' || $type == 'danger') {
                                $icon = 'fa-times-circle';
                                $colorClass = 'bg-danger-light';
                            } elseif ($type == 'warning') {
                                $icon = 'fa-wallet';
                                $colorClass = 'bg-warning-light';
                            }
                        ?>
                        <div class="notif-item <?= ($n['is_read'] == 0) ? 'unread' : '' ?>" data-id="<?= $n['id'] ?>" style="cursor: pointer;">
                            <div class="d-flex gap-3">
                                <div class="notif-icon <?= $colorClass ?>">
                                    <i class="fas <?= $icon ?> fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold mb-0 pe-4 text-white"><?= esc($n['title']) ?></h6>
                                        <small class="text-white opacity-50 text-nowrap"><?= isset($n['created_at']) ? date('d M Y, H:i', strtotime($n['created_at'])) . ' WIB' : 'Baru saja' ?></small>
                                    </div>
                                    <p class="text-white opacity-75 small mb-0 text-truncate" style="max-width: 90%;"><?= $n['message'] ?></p>
                                    <div class="d-none full-message"><?= $n['message'] ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5" id="loadMoreContainer">
                <button id="loadMoreBtn" class="btn btn-action-global rounded-pill px-5 fw-bold small text-white border border-white" style="background: rgba(255,255,255,0.1);">Muat Lebih Banyak</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let isAllLoaded = false;

$(document).ready(function() {
    const $items = $('.notif-item');
    
    // Default show only 3
    if ($items.length > 3) {
        $items.slice(3).hide();
    } else {
        $('#loadMoreContainer').hide();
    }

    // Load More click
    $('#loadMoreBtn').on('click', function() {
        $items.show();
        $(this).hide();
        isAllLoaded = true;
    });

    // Search functionality
    $('#notificationSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        if (query.trim() === '') {
            // Restore default view
            if (!isAllLoaded) {
                $items.hide().slice(0, 3).show();
                $('#loadMoreContainer').show();
            } else {
                $items.show();
            }
        } else {
            // Filter mode
            $('#loadMoreContainer').hide();
            $items.each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(query));
            });
        }
    });

    // Click Notification to view details
    $(document).on('click', '.notif-item', function(e) {
        let id = $(this).data('id');
        let title = $(this).find('h6').text();
        let message = $(this).find('.full-message').html(); // use html to keep formatting
        let isUnread = $(this).hasClass('unread');

        Swal.fire({
            title: title,
            html: `<div class="text-start" style="font-size: 0.95rem; line-height: 1.6;">${message}</div>`,
            icon: 'info',
            confirmButtonColor: '#ce2127',
            confirmButtonText: 'Tutup'
        });

        if (isUnread) {
            markRead(id, this);
        }
    });
});

function markRead(id, element) {
    $.post('<?= base_url("pelatihan/peserta/notifikasi/read/") ?>' + id, function(response) {
        if(response.success) {
            $(element).removeClass('unread');
            
            // Update badge count in header if exists
            var badge = $('.badge.bg-danger.rounded-pill');
            if(badge.length > 0) {
                var current = parseInt(badge.text());
                if(current > 1) {
                    badge.text(current - 1);
                } else {
                    badge.remove();
                    $('.top-0.start-100').remove(); // Remove the dot
                }
            }
        }
    });
}

function markAllRead() {
    $.post('<?= base_url("pelatihan/peserta/notifikasi/read_all") ?>', function(response) {
        if(response.success) {
            $('.notif-item.unread').removeClass('unread');
            $('.badge.bg-danger.rounded-pill').remove();
            $('.top-0.start-100').remove();
        }
    });
}
</script>
<?= $this->endSection() ?>
