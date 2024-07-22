<?php
/**
 * @var \yii\web\View $this
 */

$this->title = 'Pencatatan Pakan Ternak' ?? $this->params['pageTitle'] ?? 'MataSapi';
?>
<div class="page-content"> 
    <section class="row">
        <div class="col-12 col-lg-9">
            <!-- <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldShow"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Sapi Saya</h6>
                                    <h6 class="font-extrabold mb-0">8</h6>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card"> 
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Sapi</h6>
                                    <h6 class="font-extrabold mb-0">203.000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldAdd-User"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Following</h6>
                                    <h6 class="font-extrabold mb-0">80.000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldBookmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Saved Post</h6>
                                    <h6 class="font-extrabold mb-0">112</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <section class="section">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Catatan Ternak</h4>
                            <form class="form" method="post">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">VID Sapi</label>
                                        <select class="choices form-select">
                                                <option value="romboid">A001X</option>
                                                <option value="trapeze">21F09</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Kandang</label>
                                        <select class="choices form-select">
                                        <optgroup label="Kode Sapi">
                                            <option value="red">AAS92</option>
                                            <option value="green">BCS01</option>
                                        </optgroup>
                                    </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Biaya</label>
                                        Rp. <input type="integer" id="feedback1" class="form-control" placeholder=""
                                            name="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="feedback1" class="sr-only">Detail</label>
                                        <input type="integer" id="feedback1" class="form-control" placeholder=""
                                            name="name">
                                        <!-- <div id="snow"></div> -->
                                    </div>
                                </div>
                                <div class="form-actions d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary me-1">Submit</button>
                                    <button type="reset" class="btn btn-light-primary">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h4>Catatan Terkini</h4>
                    <div class="form-group position-relative  mb-0 has-icon-left">
                        <input type="text" class="form-control" placeholder="Cari catatan...">
                        <div class="form-control-icon">
                            <svg class="bi" width="1.5em" height="1.5em" fill="currentColor">
                                <use
                                    xlink:href="assets/static/images/bootstrap-icons.svg#search" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">A001X</h5>
                                <small>Baru saja</small>
                            </div>
                            <p class="mb-1">
                                Donec id elit non mi porta gravida at eget metus. Maecenas sed
                                diam eget risus varius blandit...
                            </p>
                            <small>Rp. 5.000.000</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">A001X</h5>
                                <small>2 Jam lalu</small>
                            </div>
                            <p class="mb-1">
                                Donec id elit non mi porta gravida at eget metus. Maecenas sed
                                diam eget risus varius blandit...
                            </p>
                            <small>Rp. 5.000.000</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">A001X</h5>
                                <small>2 Jam lalu</small>
                            </div>
                            <p class="mb-1">
                                Donec id elit non mi porta gravida at eget metus. Maecenas sed
                                diam eget risus varius blandit...
                            </p>
                            <small>Rp. 5.000.000</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">A001X</h5>
                                <small>2 Jam lalu</small>
                            </div>
                            <p class="mb-1">
                                Donec id elit non mi porta gravida at eget metus. Maecenas sed
                                diam eget risus varius blandit...
                            </p>
                            <small>Rp. 5.000.000</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">A001X</h5>
                                <small>2 Jam lalu</small>
                            </div>
                            <p class="mb-1">
                                Donec id elit non mi porta gravida at eget metus. Maecenas sed
                                diam eget risus varius blandit...
                            </p>
                            <small>Rp. 5.000.000</small>
                        </a>
                        
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-primary mt-3">
                                <li class="page-item"><a class="page-link" href="#">
                                        <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                    </a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">
                                        <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>