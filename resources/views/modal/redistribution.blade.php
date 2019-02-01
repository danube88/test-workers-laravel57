<!-- redistributionModal -->
<div class="modal fade bd-example-modal-lg" id="redistributionModal" tabindex="-1" role="dialog" aria-labelledby="redistributionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="row">
          <div class="col-12 col-sm-12">
            <h5 class="modal-title" id="redistributionModalLabel">Перераспределение</h5>
          </div>
        </div>
        <button type="button" class="btn close" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="Форма для перераспределения сотрудников, с одного начальника на другого. Должность у выбраных сотрудников должна быть одинаковая!">
          <i class="fa fa-question fa-lg" aria-hidden="true"></i>
        </button>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="redisHeadName col-12 col-sm-12">
              <label for="title">"С" таб.номер: Начальник (Ф.И.О. / должность)</label>
              <div class="input-group">
                <input type="text" id="redisHeadName" list="redislistWorkers" class="form-control" value=""/>
                <datalist id="redislistWorkers">
                  @foreach($guide as $value)
                    <option data-id="{{ $value->id }}" data-idpos="{{ $value->id_p }}" value="{{ $value->table_number}}: {{ $value->nameWorker}} / {{$value->name_position }}"></option>
                  @endforeach
                </datalist>
                <div class="input-group-append">
                  <button id="redisCleanHead" type="button" class="btn btn-secondary">
                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="atredisHeadName col-12 col-sm-12 d-none">
              <label for="title">"На" таб.номер: Начальник (Ф.И.О. / должность)</label>
              <div class="input-group">
                <input type="text" id="atredisHeadName" list="atredislistWorkers" class="form-control" value=""/>
                <datalist id="atredislistWorkers">
                </datalist>
                <div class="input-group-append">
                  <button id="atredisCleanHead" type="button" class="btn btn-secondary">
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
        <button id="redisSave" type="button" class="btn btn-primary">Перераспределить</button>
      </div>
    </div>
  </div>
</div>
