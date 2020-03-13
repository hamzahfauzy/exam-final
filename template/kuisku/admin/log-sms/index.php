<?php 
$this->title .= " | SMS Log"; 
$this->visited = "logsms";

$this->js = [
    asset('js/sweetalert2@9.js'),
    asset('js/sweetalert2.min.js'),
];
?>
<link rel="stylesheet" href="<?= asset('css/wordpress-admin.css') ?>">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <h2>SMS Log</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <table class="table table-bordered table-striped table-kuis">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No HP</th>
                            <th>Konten</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($smsLog)): ?>
                        <tr>
                            <td colspan="5"><i>Tidak ada data!</i></td>
                        </tr>
                        <?php endif ?>
                        <?php foreach($smsLog as $key => $log): ?>
                        <tr>
                            <td><?= ++$key ?></td>
                            <td><?= $log->no_hp ?></td>
                            <td><?= $log->pesan ?></td>
                            <td><?= $log->status ?></td>
                            <td style="white-space:nowrap"><?= $log->date ?></td>
                            <td style="white-space:nowrap">
                                <button href="javascript:void(0)" onclick="kirimNotifikasi(<?=$log->id?>,this)" class="btn btn-primary"><i class="fa fa-send"></i> Resend</button>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
async function kirimNotifikasi(log_id, el)
{
    Swal.fire({
        title: 'Konfirmasi ?',
        text: "Apakah anda yakin akan mengirim ulang notifikasi kepada peserta ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya'
    }).then(async (result) => {
        if (result.value) {
            el.innerHTML = "Loading..."
            let request = await fetch('<?= route('admin/log-sms/resend') ?>',{
                method :'POST',
                headers : {
                    'Content-Type':'application/json'
                },
                body   : JSON.stringify({log_id:log_id}),
            })
            let response = await request.json()

            if(response.status == false)
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Notifikasi gagal terkirim!',
                    footer: '<a href="javascript:void(0)">Terdapat kesalahan pada saat pengiriman sms</a>'
                })
            }
            else
            {
                Swal.fire(
                    'Success!',
                    'Notifikasi Berhasil di kirim.',
                    'success'
                )
            }
            el.innerHTML = '<i class="fa fa-send"></i> Resend'
        }
    })
}
</script>