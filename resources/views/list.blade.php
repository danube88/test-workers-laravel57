@extends('layouts.sample')

@section('head')
  <!-- CSS -->
  {{ Html::style('css/bootstrap.min.css') }}
  {{ Html::style('css/font-awesome.min.css') }}
  {{ Html::style('css/dataTables.bootstrap4.min.css') }}
  {{ Html::style('css/listStyles.css') }}

  {{ Html::script('js/jquery.min.js') }}
  {{ Html::script('js/popper.min.js') }}
  {{ Html::script('js/jquery.dataTables.min.js') }}
  {{ Html::script('js/dataTables.bootstrap4.min.js') }}
  {{ Html::script('js/bootstrap.min.js') }}
@endsection

@push('scripts_head')
@endpush

@section('header')
  @include('parts.navbar')
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-sm-12">
      <h1 class="h2 page-header">Список сотрудников</h1>
      <br />
      <div class="table-responsive">
        <table id="tableWorkers" class="table table-sm table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Фото</th>
              <th>Ф.И.О.</th>
              <th>Должность</th>
              <th>Ф.И.О. начальника</th>
              <th>Дата рождения</th>
              <th>Дата приема на работу</th>
              <th>Размер заработной платы</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer')
@endsection

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $(".list").addClass('active');
    $('#tableWorkers').DataTable({
      processing: true,
      serverSide: true,
      cache: false,
      ajax: "{!! route('dataWorkers') !!}",
      headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
      },
      columns: [
          { data: 'table_number', name: 'table_number' },
          { data: 'photo', name: 'photo',
            render: function(data, type, row) {
                return '<img src="'+data+{{--/*'?'+Math.random()+*/--}}'" width="70px" height="105px" />';
              }, orderable: false, searchable: false },
          { data: 'nameWorker', name: 'nameWorker', width: '200px' },
          { data: 'position', name:'position' },
          { data: 'nameHead', name: 'nameHead', width: '200px' },
          { data: 'birthday', name: 'birthday', width: '80px' },
          { data: 'reception_date', name: 'reception_date', width: '80px' },
          { data: 'salary', name: 'salary', width: '110px' }
      ],
      pageLength: 10,
      language: {
        "loadingRecords": "Загрузка...",
        "processing": "Подождите...",
        "lengthMenu": "Выводить _MENU_ записей на страницу",
        "zeroRecords": "Ничего не найдено, извините",
        "info": "Показано страниц _PAGE_ из _PAGES_ (_MAX_ записей)",
        "infoEmpty": "Нет данных",
        "infoFiltered": "(фильтр по _MAX_ кол-ву записей)",
        "search": "Поиск:",
        "paginate": {
          "next": "Cледующий",
          "previous": "Предыдущий"
        }
      }
    });
  });
</script>
@endpush
