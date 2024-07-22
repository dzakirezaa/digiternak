<?php
/**
 * @var \yii\web\View $this
 */

$this->title = 'Daftar Sapi' ?? $this->params['pageTitle'] ?? 'MataSapi';
?>
<div class="page-content"> 
    <section class="row">
        <div class="col-12">
            <section class="section">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Buat Sapi Baru</h4>
                            <form class="form" method="post">
                                <div class="form-body row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Nama Sapi</label>
                                            <input type="text" id="feedback1" class="form-control" placeholder=""
                                                name="name">
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Lokasi Kandang</label>
                                            <input type="text" id="feedback1" class="form-control" placeholder=""
                                                name="location">
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">ID Kandang</label>
                                            <select class="choices form-select">
                                                    <option value="romboid">1</option>
                                                    <option value="trapeze">2</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Tipe Sapi</label>
                                            <select class="choices form-select">
                                                    <option value="romboid">1</option>
                                                    <option value="trapeze">2</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Peranakan Sapi</label>
                                            <select class="choices form-select">
                                                    <option value="romboid">1</option>
                                                    <option value="trapeze">2</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Tipe Maintenance</label>
                                            <select class="choices form-select">
                                                    <option value="romboid">1</option>
                                                    <option value="trapeze">2</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Sumber</label>
                                            <select class="choices form-select">
                                                    <option value="romboid">1</option>
                                                    <option value="trapeze">2</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                        <label for="feedback1" class="sr-only">Kepemilikan</label>
                                        <select class="choices form-select">
                                                <option value="romboid">1</option>
                                                <option value="trapeze">2</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Status Kehamilan</label>
                                        <select class="choices form-select">
                                                <option value="romboid">1</option>
                                                <option value="trapeze">2</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Jenis Kelamin</label>
                                        <select class="choices form-select">
                                                <option value="romboid">1</option>
                                                <option value="trapeze">2</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Umur</label>
                                        <select class="choices form-select">
                                                <option value="romboid">1</option>
                                                <option value="trapeze">2</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Ukuran Dada</label>
                                        <input type="text" id="feedback1" class="form-control" placeholder=""
                                            name="location">
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Berat</label>
                                        <input type="text" id="feedback1" class="form-control" placeholder=""
                                            name="location">
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Kondisi</label>
                                        <select class="choices form-select">
                                                <option value="romboid">Sehat</option>
                                                <option value="trapeze">2</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-actions d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h4>Sapi Terdaftar</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Sapi</th>
                                    <th>VID</th>
                                    <th>Umur</th>
                                    <th>Kesehatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-bold-500">Sapi ijo</td>
                                    <td>2938</td>
                                    <td>2 Tahun</td>
                                    <td>Sehat</td>
                                    <td><div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#border-less"><i class="bi bi-eye-fill"></i> Show</button>
                                        <button class="btn icon icon-left btn-warning me-2 text-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
                                        <button class="btn icon icon-left btn-danger me-2 text-nowrap"><i class="bi bi-x-circle"></i> Remove</button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td class="text-bold-500">Sapi ijo</td>
                                    <td>2938</td>
                                    <td>2 Tahun</td>
                                    <td>Sehat</td>
                                    <td><div class="comment-actions">
                                        <button class="btn icon icon-left btn-primary me-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#border-less"><i class="bi bi-eye-fill"></i> Show</button>
                                        <button class="btn icon icon-left btn-warning me-2 text-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
                                        <button class="btn icon icon-left btn-danger me-2 text-nowrap"><i class="bi bi-x-circle"></i> Remove</button>
                                    </div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--BorderLess Modal Modal -->
<div class="modal fade text-left modal-borderless" id="border-less" tabindex="-1"
role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Border-Less</h5>
            <button type="button" class="close rounded-pill" data-bs-dismiss="modal"
                aria-label="Close">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="modal-body">
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item text-bold-500">Nama Sapi</li>
                <li class="list-group-item">Sapi Ijo</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Lokasi Kandang</li>
                <li class="list-group-item">Bogor</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Tipe Sapi</li>
                <li class="list-group-item">Bison</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Peranakan Sapi</li>
                <li class="list-group-item">Sapi Tetangga</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Tipe Maintenance</li>
                <li class="list-group-item">Baik</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Sumber</li>
                <li class="list-group-item">Sendiri</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Kepemilikan</li>
                <li class="list-group-item">Sendiri</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Status Kehamilan</li>
                <li class="list-group-item">Tidak Bisa Hamil</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Jenis Kelamin</li>
                <li class="list-group-item">Laki-Laki</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Umur</li>
                <li class="list-group-item">2 Bulan</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Ukuran Dada</li>
                <li class="list-group-item">-</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Berat</li>
                <li class="list-group-item">120kg</li>
            </ul>
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item">Kondisi</li>
                <li class="list-group-item">Sehat Alhamdulillah</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button class="btn icon icon-left btn-warning me-2 text-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
            <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">
                <i class="bx bx-x d-block d-sm-none"></i>
                <span class="d-none d-sm-block">Close</span>
            </button>
        </div>
    </div>
</div>
</div>
{% endblock %}
{% block js %}
<link rel="stylesheet" href="assets/extensions/quill/quill.snow.css">
<link rel="stylesheet" href="assets/extensions/quill/quill.bubble.css">
<script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="assets/static/js/pages/dashboard.js"></script>
<script src="assets/extensions/quill/quill.min.js"></script>
<script src="assets/static/js/pages/quill.js"></script>
{% endblock %}