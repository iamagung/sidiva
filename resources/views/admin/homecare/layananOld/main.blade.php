@extends('layouts.index')

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endpush
@section('content')
<div class="page-content">
    <!-- judul dan link -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="pe-3">
            <span style="font-weight: bold;">{{$title}}<span>
        </div>
    </div>
    <!-- main content -->
    <div class="card main-layer">
        <div class="card-header">
            <h5>Jenis Layanan</h5>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: 2rem">
                <div id="message"></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 90%">Nama Layanan</th>
                                <th style="width: 10%">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function(){
        fetch_data();

        // FUNC FETCH DATA
        function fetch_data() {
            $.ajax({
                url:"{{ route('layananhc.fetch_data')}}",
                dataType:"json",
                success:function(data) {
                    var html = '';
                    html += '<tr>';
                    html += '<td contenteditable id="nama_layanan"></td>';
                    html += '<td><button type="button" class="btn btn-success btn-xs" id="add">Tambah</button></td></tr>';
                    for(var count=0; count < data.length; count++)
                    {
                        html +='<tr>';
                        html +='<td contenteditable class="column_name" data-column_name="nama_layanan" data-id="'+data[count].id_layanan_hc+'">'+data[count].nama_layanan+'</td>';
                        html += '<td>';
                        // html += '<button type="button" class="btn btn-primary btn-xs update" id="'+data[count].id_layanan_hc+'">Update</button>&nbsp;';
                        html += '<button type="button" class="btn btn-danger btn-xs delete" id="'+data[count].id_layanan_hc+'">Hapus</button>';
                        html += '</td>';
                        html +='/<tr>';
                    }

                    $('tbody').html(html);
                }
            });
        }

        var _token = $('input[name="_token"]').val();

        //FUNC ADD
        $(document).on('click', '#add', function(){
            var nama_layanan = $('#nama_layanan').text();
            if(nama_layanan != '') {
                $.ajax({
                url:"{{ route('layananhc.add_data') }}",
                method:"POST",
                data:{nama_layanan:nama_layanan, _token:_token},
                success:function(data)
                {
                    location.reload()
                    $('#message').html(data);
                    fetch_data();
                }
                });
            } else {
                $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
            }
        });

        // FUNC UPDATE
        $(document).on('blur', '.column_name', function(){
            var column_name = $(this).data("column_name");
            var column_value = $(this).text();
            var id = $(this).data("id");

            console.log(id)
            if(column_value != ''){
                $.ajax({
                url:"{{ route('layananhc.update_data') }}",
                method:"POST",
                data:{column_name:column_name, column_value:column_value, id:id, _token:_token},
                success:function(data)
                {
                    location.reload()
                    $('#message').html(data);
                }
                })
            } else {
                $('#message').html("<div class='alert alert-danger'>Silahkan Periksa Inputan Anda!!</div>");
            }
        });

        
        // FUNC DELETE
        $(document).on('click', '.delete', function(){
            var id = $(this).attr("id");
            Swal.fire({
                title: 'Apakah Kamu Yakin?',
                text: "Data layanan akan di hapus permanent!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:"{{ route('layananhc.delete_data') }}",
                        method:"POST",
                        data:{id:id, _token:_token},
                        success:function(data)
                        {
                            location.reload()
                            $('#message').html(data);
                            fetch_data();
                        }
                    });
                }
            });
            // if(confirm("Apakah Anda Yakin Ingin Menghapus Data ini?")) {
            //     $.ajax({
            //         url:"{{ route('layananhc.delete_data') }}",
            //         method:"POST",
            //         data:{id:id, _token:_token},
            //         success:function(data)
            //         {
            //             location.reload()
            //             $('#message').html(data);
            //             fetch_data();
            //         }
            //     });
            // }
        });
    });
</script>
@endpush

