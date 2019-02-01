@extends('layouts.sample')

@section('head')
  <!-- CSS -->
  {{ Html::style('css/bootstrap.min.css') }}
  {{ Html::style('css/font-awesome.min.css') }}
  {{ Html::style('css/dataTables.bootstrap4.min.css') }}
  {{ Html::style('css/listStylesAuth.css') }}

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
      <div class="btn-worker">
        <button id="add" type="button" class="btn btn-primary" data-toggle="modal" data-target="#cardModal" data-placement="bottom" title="Добавить нового сотрудника в БД">
          <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> Добавить
        </button>
      </div>
      <div class="btn-redistribution">
        <button id="redistribution" type="button" class="btn btn-primary" data-toggle="modal" data-target="#redistributionModal" data-placement="top" title="Перераспределение сотрудников">
          <i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Перераспределение
        </button>
      </div>
      <br/>
      <div class="table-responsive">
        <table id="tableWorkers" class="table table-sm table-striped table-bordered" cellspacing="0">
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
@include('modal.card')
@include('modal.redistribution')
@endsection

@section('footer')
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $(".list").addClass('active');

    $('[data-toggle="popover"]').popover({
      trigger: 'focus'
    })

    $('#add').tooltip();
    $('#redistribution').tooltip();
    $('#cleanHead').tooltip();
    $('#cleanPosition').tooltip();
    $('#cleanPhoto').tooltip();

    $('#editSave').tooltip();
    $('#delSave').tooltip();

    var photodel = 0;

    var table = $('#tableWorkers').DataTable({
      processing: true,
      serverSide: true,
      cache: false,
      ajax: "{!! route('homeWorkers') !!}",
      headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
      },
      columns: [
          { data: 'table_number', name: 'table_number' },
          { data: 'photo', name: 'photo',
            render: function(data, type, row) {
                return '<img src="'+data+{{--/*'?'+Math.random()+*/--}}'" width="70px" height="105px" />';
              }, orderable: false, searchable: false },
          { data: 'nameWorker', name: 'nameWorker'},
          { data: 'position', name:'position' },
          { data: 'nameHead', name: 'nameHead'},
          { data: 'birthday', name: 'birthday'},
          { data: 'reception_date', name: 'reception_date'},
          { data: 'salary', name: 'salary'}
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

    $('#tableWorkers tbody').on( 'mouseenter', 'td', function () {
      //var colIdx = table.cell(this).index().column;
      var colIdy = table.cell(this).index().row;
      //$( table.cells().nodes() ).removeClass( 'bg-dark text-white' );
      //$( table.column( colIdx ).nodes() ).addClass( 'bg-dark text-white' );
      $( table.rows().nodes() ).removeClass( 'bg-dark text-white' );
      $( table.row( colIdy ).nodes() ).addClass( 'bg-dark text-white' );
    });

    $(document).on('click', '#add', function(){
      $('.cardName').html('ДОБАВЛЕНИЕ');
      cleanCardFieldModal();
      hideFieldErrors();
      $('#addSave').removeAttr('disabled');
      $('#addSave').removeClass('d-none').addClass('d-block');
      $('#editSave').attr('disabled',true);;
      $('#editSave').removeClass('d-block').addClass('d-none');
      $('#delSave').attr('disabled',true);;
      $('#delSave').removeClass('d-block').addClass('d-none');
    });

    $("#position").change(function() {
      var position,salary;
      var val = $(this).val();
      var datalist = $('#positionList').parent();
      var selected = datalist.find('[value="'+val+'"]');

      if(selected.length > 0) {
        position = selected.data('id');
        salary = selected.data('salary');
        $('#salary').val(salary);
        if (position == 1) {
          $('#headName').val('');
          $('#headName').attr('disabled',true);
        }
        else {
          $.ajax({
            url: "{{ route('listHead') }}",
            type: "GET",
            data:{
              'position':position
            },
            headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
              if (data.errors){
                if (data.errors.position) {
                  alert(data.errors.position);
                }
              } else {
                $('#listHead').html(data);
                $('#headName').removeAttr('disabled');
                $('#headName').val('');
              }
            },
            error: function (msg) {
              alert('Ошибка');
            }
          });
        }
      } else {
        $('#salary').val('');
      }
    });

    $(document).on('click', '#cleanPosition', function(){
      $('#headName').val('');
      $('#headName').attr('disabled',true);
      $('#listHead').html('');
      $('#position').val('');
      $('#salary').val('');
    });

    $(document).on('click', '#cleanHead', function(){
      $('#headName').val('');
    });

    $(document).on('click', '#addSave',function(){
      var url = $(this).data('href');

      var valHead = $('#headName').val();
      var head_id = 0;
      if(valHead){
        var datalist = $('#listHead').parent();
        var selected = datalist.find('[value="'+valHead+'"]');
        if(selected.length > 0) {
          head_id = selected.data('id');
        } else {
          head_id = 0;
        }
      }

      var valPosition = $('#position').val();
      var position = '';
      if(valPosition){
        var datalist = $('#positionList').parent();
        var selected = datalist.find('[value="'+valPosition+'"]');
        if(selected.length > 0) {
          position = selected.data('id');
        } else {
          position = '';
        }
      }

      if($('#birthday').val() >= $('#date').val()){
        $('#errorBirthday').html('<div class="alert alert-danger" role="alert">Дата рождения должна быть ранее Даты приема на работу</div>');
        $('#errorBirthday').removeClass('d-none').addClass('d-block');
        return;
      }
      var file = $('#file')[0].files[0];
      var form_data = new FormData();
      if (file) {
        form_data.append('photo', file);
      }
      form_data.append('photodel', photodel);
      form_data.append('head_id', head_id);
      form_data.append('surname', $('#surname').val());
      form_data.append('name',$('#name').val());
      form_data.append('patronymic',$('#patronymic').val());
      form_data.append('table_number',$('#tableNumber').val());
      form_data.append('reception_date',$('#date').val());
      form_data.append('position',position);
      form_data.append('salary',$('#salary').val());
      form_data.append('birthday',$('#birthday').val());

      hideFieldErrors();

      $.ajax({
        url: url,
        type: "POST",
        cache: false,
        processData: false,
        contentType: false,
        data: form_data,
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          if (data.errors) {
            errorOutput(data.errors);
          } else {
            $('#cardModal').modal('hide');
            alert(data.data);
            cleanCardFieldModal();
            $('#tableWorkers').DataTable().ajax.reload();
          }
        },
        error: function (msg) {
          alert('Errors');
        }
      });
    });

    $('#tableWorkers tbody').on( 'click', 'tr', function () {
      var tableNumber = $(this)["0"].firstChild.innerHTML;

      $.ajax({
        url: "{{ route('dataWorker') }}",
        type: "GET",
        cache: false,
        data: {
          'table_number':tableNumber
        },
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          if(data.headworker){
            $('#listHead').html(data.listHead);
            $('#headName').val(data.headworker);
            $('#headName').removeAttr('disabled');
          } else {
            $('#headName').val('');
            if(data.level != 1){
              $('#headName').removeAttr('disabled');
              $('#listHead').html(data.listHead);
            } else {
              $('#headName').attr('disabled',true);
              $('#listHead').html('');
            }
          }

          $('#tableNumber').val(data.table_number);
          $('#surname').val(data.surname);
          $('#name').val(data.name);
          $('#patronymic').val(data.patronymic);
          $('#birthday').val(data.birthday);
          $('#position').val(data.positionName);
          $('#salary').val(data.salary);
          $('#date').val(data.reception_date);
          $('#photo').attr('src',data.photo{{--/*+'?'+Math.random()*/--}});
          photodel = 0;
          $('.cardName').html('ИЗМИНЕНИЕ');

          $('#addSave').attr('disabled',true);
          $('#addSave').removeClass('d-block').addClass('d-none');
          $('#editSave').removeAttr('disabled');
          $('#editSave').removeClass('d-none').addClass('d-block');
          $('#delSave').removeAttr('disabled');
          $('#delSave').removeClass('d-none').addClass('d-block');
          $('#cardModal').modal('show');
        },
        error: function (msg) {
          alert('Errors');
        }
      });
    });

    $(document).on('click', '#delSave',function(){
      var url = "{{ route('indexWorker') }}"+"/"+$('#tableNumber').val();
      $.ajax({
        url: url,
        type: "DELETE",
        data: {
        },
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          if (data.errors) {
            alert(data.errors.data);
          } else {
            alert(data.data);
            cleanCardFieldModal();
            $('#cardModal').modal('hide');
            $('#tableWorkers').DataTable().ajax.reload();
          }
        },
        error: function (msg) {
          alert('Errors');
        }
      });
    });

    $(document).on('click', '#editSave',function(){
      var url = "{{ route('indexWorker') }}"+"/"+$('#tableNumber').val();

      var valHead = $('#headName').val();
      var head_id = 0;
      if(valHead){
        var datalist = $('#listHead').parent();
        var selected = datalist.find('[value="'+valHead+'"]');
        if(selected.length > 0) {
          head_id = selected.data('id');
        } else {
          head_id = 0;
        }
      }

      var valPosition = $('#position').val();
      var position = '';
      if(valPosition){
        var datalist = $('#positionList').parent();
        var selected = datalist.find('[value="'+valPosition+'"]');
        if(selected.length > 0) {
          position = selected.data('id');
        } else {
          position = '';
        }
      }

      if($('#birthday').val() >= $('#date').val()){
        $('#errorBirthday').html('<div class="alert alert-danger" role="alert">Дата рождения должна быть ранее Даты приема на работу</div>');
        $('#errorBirthday').removeClass('d-none').addClass('d-block');
        return;
      }

      var file = $('#file')[0].files[0];

      var form_edit = new FormData();
      if (file) {
        form_edit.append('photo', file);
      }
      form_edit.append('photodel', photodel);
      form_edit.append('_method', 'PUT');
      form_edit.append('head_id', head_id);
      form_edit.append('surname', $('#surname').val());
      form_edit.append('name',$('#name').val());
      form_edit.append('patronymic',$('#patronymic').val());
      form_edit.append('table_number',$('#tableNumber').val());
      form_edit.append('reception_date',$('#date').val());
      form_edit.append('position',position);
      form_edit.append('salary',$('#salary').val());
      form_edit.append('birthday',$('#birthday').val());

      hideFieldErrors();

      $.ajax({
        url: url,
        type: "POST",
        cache: false,
        processData: false,
        contentType: false,
        data: form_edit,
        headers: {
          'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          if (data.errors) {
            errorOutput(data.errors);
          } else {
            $('#cardModal').modal('hide');
            alert(data.data);
            cleanCardFieldModal();
            $('#tableWorkers').DataTable().ajax.reload();
          }
        },
        error: function (msg) {
          alert('Errors');
        }
      });
    });

    $('#file').change(function() {
      var input = $(this)[0];
      if (input.files && input.files[0]) {
        if (input.files[0].type.match('image.*')) {
          if (input.files[0].size > 1048576) {
            alert('Pазмер файла более 1MB');
          } else {
            var reader = new FileReader();
            reader.onload = function (e) {
              $('#photo').attr('src', e.target.result);
              $('.file').html(input.files[0].name);
              photodel = 0;
            }
              reader.readAsDataURL(input.files[0]);
          }
        } else {
          alert('Oшибка, не изображение');
        }
      } else {
        alert('Error!');
      }
    });

    $(document).on('click', '#cleanPhoto', function(){
      $('#photo').attr('src','../img/example.jpg');
      $('.file').html('Выберите файл');
      $('#file').val('');
      photodel = 1;
    });

    $(document).on('click', '#redistribution',function(){
      cleanRedistributionFieldModal();
    });

    $(document).on('click', '#redisCleanHead', function(){
      cleanRedistributionFieldModal();
    });

    $(document).on('click', '#atredisCleanHead', function(){
      $('#atredisHeadName').val('');
    });

    $("#redisHeadName").change(function() {
      $('#atredisHeadName').val('');
      var val = $(this).val();
      var datalist = $('#redislistWorkers').parent();
      var selected = datalist.find('[value="'+val+'"]');
      if(selected['0'].value != '') {
        $.ajax({
          url: "{{ route('listRedistributionWorkers') }}",
          type: "POST",
          data:{
            'id':selected.data('id'),
            'position':selected.data('idpos'),
          },
          headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (data) {
            if(data != ''){
              $('#atredislistWorkers').html(data);
              $(".atredisHeadName").removeClass('d-none').addClass('d-block');
            } else {
              alert('Нет подходящих сотрудников');
            }
          },
          error: function (msg) {
            alert('Errors');
          }
        });
      } else {
        $(".atredisHeadName").removeClass('d-block').addClass('d-none');
      }
    });

    $(document).on('click','#redisSave',function(){
      var oneDataList = $('#redislistWorkers').parent();
      var oneHead = oneDataList.find('[value="'+$('#redisHeadName').val()+'"]');

      var twoDataList = $('#atredislistWorkers').parent();
      var twoHead = twoDataList.find('[value="'+$('#atredisHeadName').val()+'"]');

      if(oneHead.length < 0 || twoHead.leght < 0) {
        alert('Errors: Данные не верные');
      } else {
        if(oneHead['0'].value != '' && twoHead['0'].value != '') {
          var oneId = oneHead.data('id');
          var twoId = twoHead.data('id');
          $.ajax({
            url: "{{ route('redistribution') }}",
            type: "POST",
            data:{
              'oneId':oneId,
              'twoId':twoId,
            },
            headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
              if(data.errors){
                alert(data.errors);
              }
              alert(data.data);
              $('#redistributionModal').modal('hide');
              cleanRedistributionFieldModal();
              $('#tableWorkers').DataTable().ajax.reload();
            },
            error: function (msg) {
              alert('Errors');
            }
          });
        } else {
          alert('Errors: Не все поля заполнены!');
        }
      }
    });

    function errorOutput(error){
      if(error.table_number){
        $('#errorTableNumber').html('<div class="alert alert-danger" role="alert">'+error.table_number+'</div>');
        $('#errorTableNumber').removeClass('d-none').addClass('d-block');
      }
      if(error.head_id){
        $('#errorHeadName').html('<div class="alert alert-danger" role="alert">'+error.head_id+'</div>');
        $('#errorHeadName').removeClass('d-none').addClass('d-block');
      }
      if(error.surname){
        $('#errorSurname').html('<div class="alert alert-danger" role="alert">'+error.surname+'</div>');
        $('#errorSurname').removeClass('d-none').addClass('d-block');
      }
      if(error.name){
        $('#errorName').html('<div class="alert alert-danger" role="alert">'+error.name+'</div>');
        $('#errorName').removeClass('d-none').addClass('d-block');
      }
      if(error.patronymic){
        $('#errorPatronymic').html('<div class="alert alert-danger" role="alert">'+error.patronymic+'</div>');
        $('#errorPatronymic').removeClass('d-none').addClass('d-block');
      }
      if(error.birthday){
        $('#errorBirthday').html('<div class="alert alert-danger" role="alert">'+error.birthday+'</div>');
        $('#errorBirthday').removeClass('d-none').addClass('d-block');
      }
      if(error.position){
        $('#errorPosition').html('<div class="alert alert-danger" role="alert">'+error.position+'</div>');
        $('#errorPosition').removeClass('d-none').addClass('d-block');
      }
      if(error.salary){
        $('#errorSalary').html('<div class="alert alert-danger" role="alert">'+error.salary+'</div>');
        $('#errorSalary').removeClass('d-none').addClass('d-block');
      }
      if(error.reception_date){
        $('#errorDate').html('<div class="alert alert-danger" role="alert">'+error.reception_date+'</div>');
        $('#errorDate').removeClass('d-none').addClass('d-block');
      }
      if(error.photo){
        $('#errorPhoto').html('<div class="alert alert-danger" role="alert">'+error.photo+'</div>');
        $('#errorPhoto').removeClass('d-none').addClass('d-block');
      }
      if(error.photodel){
        console.log(error.photodel);
      }
    }

    function hideFieldErrors(){
      $('#errorTableNumber').removeClass('d-block').addClass('d-none');
      $('#errorHeadName').removeClass('d-block').addClass('d-none');
      $('#errorSurname').removeClass('d-block').addClass('d-none');
      $('#errorName').removeClass('d-block').addClass('d-none');
      $('#errorPatronymic').removeClass('d-block').addClass('d-none');
      $('#errorBirthday').removeClass('d-block').addClass('d-none');
      $('#errorPosition').removeClass('d-block').addClass('d-none');
      $('#errorSalary').removeClass('d-block').addClass('d-none');
      $('#errorDate').removeClass('d-block').addClass('d-none');
      $('#errorPhoto').removeClass('d-block').addClass('d-none');
    }

    function cleanRedistributionFieldModal() {
      $('#redisHeadName').val('');
      $('#atredisHeadName').val('');
      $('#atredislistWorkers').html('');
      $(".atredisHeadName").removeClass('d-block').addClass('d-none');
    }

    function cleanCardFieldModal() {
      $('#headName').val('');
      $('#headName').attr('disabled',true);
      $('#listHead').html('');
      $('#tableNumber').val('');
      $('#photo').attr('src','../img/example.jpg');
      $('.file').html('Выберите файл');
      $('#file').val('');
      $('#surname').val('');
      $('#name').val('');
      $('#patronymic').val('');
      $('#birthday').val('');
      $('#position').val('');
      $('#salary').val('');
      $('#date').val('');
      photodel = 0;
    };
  });
</script>
@endpush
