<!-- cardModal -->
<div class="modal fade bd-example-modal-lg" id="cardModal" tabindex="-1" role="dialog" aria-labelledby="cardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row">
          <div class="col-12 col-sm-12">
            <h5 class="modal-title cardName"></h5>
            <div id="errorTableNumber" class="d-none"></div>
          </div>
          <div class="col-8 col-sm-7">
            <h5 class="modal-title" id="cardModalLabel">Карточка сотрудника №</h5><sub>(6 цифр)</sub>
          </div>
          <div class="col-4 col-sm-5">
            <input type="number" id="tableNumber" class="form-control form-control-sm" value=""/>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 col-sm-6 center">
              <label for="title">Фото</label>
              <div id="errorPhoto" class="d-none"></div>
              <div class="cleanPhoto">
                <button id="cleanPhoto" type="button" class="btn btn-secondary" data-placement="left" title="Удалить фото">
                  <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                </button>
              </div>
              <img id="photo" src="../img/example.jpg" alt="..." width="200px" class="rounded mx-auto d-block img-fluid img-thumbnail"/>
              <sub>(*Ремомендация фото: 200х300px, размер файла не более 1МВ)</sub>
              <br/><br/>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" accept="image/*" lang="ru"/>
                <label class="file custom-file-label" for="customFile">Выберите файл</label>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              {{ Form::label('title','Фамилия') }}
              <div id="errorSurname" class="d-none"></div>
              {{ Form::text('surname',null,['id'=>'surname','class'=>'form-control form-control-sm','placeholder'=>'Введите: Фамилию']) }}
              {{ Form::label('title','Имя') }}
              <div id="errorName" class="d-none"></div>
              {{ Form::text('name',null,['id'=>'name','class'=>'form-control form-control-sm','placeholder'=>'Введите: Имя']) }}
              {{ Form::label('title','Отчество') }}
              <div id="errorPatronymic" class="d-none"></div>
              {{ Form::text('patronymic',null,['id'=>'patronymic','class'=>'form-control form-control-sm','placeholder'=>'Введите: Отчество']) }}
              <div class="form-row">
                <div class="form-group col-lg-6">
                  {{ Form::label('title','Дата рождения') }}
                  <div id="errorBirthday" class="d-none"></div>
                  {{ Form::input('date','birthday',null,['id'=>'birthday','class'=>'form-control form-control-sm']) }}
                </div>
                <div class="form-group col-lg-6">
                  {{ Form::label('title','Дата приема на работу') }}
                  <div id="errorDate" class="d-none"></div>
                  {{ Form::input('date','date',null,['id'=>'date','class'=>'form-control form-control-sm']) }}
                </div>
              </div>
              {{ Form::label('title','Должность') }}
              <div id="errorPosition" class="d-none"></div>
              <div class="input-group">
                {{ Form::input('text','position',null,['id'=>'position','list'=>'positionList','class'=>'form-control form-control-sm','placeholder'=>'Выберите: Должность']) }}
                <datalist id="positionList">
                  @foreach ($positions as $position)
                    <option data-salary="{{ $position->default_salary }}" data-id="{{ $position->id }}" value="{{ $position->id }}. {{ $position->name_position }}"></option>
                  @endforeach
                </datalist>
                <div class="input-group-append">
                  <button id="cleanPosition" type="button" class="btn btn-secondary" data-placement="left" title="Очистить поле">
                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
              <label for="title">Размер заработной платы</label>
              <div id="errorSalary" class="d-none"></div>
              <div class="input-group">
                <input type="number" id="salary" class="form-control form-control-sm" step="0.01" value="0.00" />
                <div class="input-group-append">
                  <span class="input-group-text">&#8381;</span>
                  <span class="input-group-text">0,00</span>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label for="title">Начальник (Таб.номер.Ф.И.О. / должность)</label>
              <div class="input-group">
                <div id="errorHeadName" class="d-none"></div>
                <input type="text" id="headName" list="listHead" class="form-control form-control-sm" value="" placeholder="Выберите: Начальника" disabled/>
                <datalist id="listHead">
                </datalist>
                <div class="input-group-append">
                  <button id="cleanHead" type="button" class="btn btn-secondary" data-placement="left" title="Очистить поле">
                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button id="addSave" type="button" class="btn btn-primary" data-href="{{ route('addWorker') }}">Сохранить</button>
        <button id="editSave" type="button" class="btn btn-primary d-none" data-placement="left" title="Изменить данные сотрудника в БД" disabled>Изменить</button>
        <button id="delSave" type="button" class="btn btn-danger d-none" data-placement="left" title="Удалить карточку сотрудника с БД" disabled>Удалить</button>
      </div>
    </div>
  </div>
</div>
