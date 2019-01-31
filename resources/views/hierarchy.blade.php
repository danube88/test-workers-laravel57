@extends('layouts.sample')

@section('head')
  <!-- CSS -->
  {{ Html::style('css/bootstrap.min.css') }}
  {{ Html::style('css/font-awesome.min.css') }}
  {{ Html::style('css/hierarchy.css') }}
  {{ Html::script('js/jquery.min.js') }}
  {{ Html::script('js/popper.min.js') }}
  {{ Html::script('js/bootstrap.min.js') }}
@endsection

@push('scripts_head')
@endpush

@section('header')
  @include('parts.navbar')
@endsection

@section('content')
<div class="container">
  <ul class="list-group Container" id="tree">
    @foreach ($workers as $worker)
      @if($worker->head_id == null)
        <li id="{{ $worker->id }}" class="list-group-item Node IsRoot {{ ($worker->count != 0) ? 'ExpandOpen':'ExpandLeaf' }}">
        @if($worker->count >= 1)
          <div class="fa fa-minus-square-o fa-lg Expand"></div>
        @else
          <div class="fa fa-minus fa-lg Expand"></div>
        @endif
          <div class="Content"><h6>{!! $worker->name_position !!}</h6>{!! $worker->nameWorker !!}</div>
          <ul class="list-group Container">
            @foreach ($workers as $value)
              @if($value->head_id == $worker->id)
              <li id="{{ $value->id }}" class="list-group-item Node {{ ($value->count != 0) ? 'ExpandClosed':'ExpandLeaf' }}">
                <div class="fa {{ ($value->count != 0) ? 'fa-plus-square-o':'fa-minus' }} fa-lg Expand"></div>
                <div class="Content"><h6>{!! $value->name_position !!}</h6>{!! $value->nameWorker !!}</div>
                <ul class="list-group Container">
                </ul>
              </li>
              @endif
            @endforeach
          </ul>
        </li>
      @endif
    @endforeach
  </ul>
</div>
@endsection

@section('footer')
@endsection

@push('scripts')
<script>
  $(document).ready(function() {

    $(".hierarchy").addClass('active');

    var element = document.getElementById('tree');
    var url = '{{ route("dataHierarchy") }}';

    function hasClass(elem, className) {
      return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className);
    }

    function toggleNode(node) {
      // {{--определить новый класс для узла--}}
      var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen';
      var element = node.getElementsByTagName('div')[0];
      var newElem = hasClass(element, 'fa-minus-square-o') ? 'fa-plus-square-o' : 'fa-minus-square-o';
      // {{--заменить текущий класс на newClass
      // регексп находит отдельно стоящий open|close и меняет на newClass--}}
      var re =  /(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/;
      node.className = node.className.replace(re, '$1'+newClass+'$3');
      var de =  /(^|\s)(fa-minus-square-o|fa-plus-square-o)(\s|$)/;
      element.className = element.className.replace(de, '$1'+newElem+'$3');
    }

    function load(node) {

      function showLoading(on) {
        var expand = node.getElementsByTagName('div')[0];
        expand.className = on ? 'fa fa-spinner fa-lg ExpandLoading' : 'fa fa-minus-square-o fa-lg Expand';
      }

      function onSuccess(data) {
        if (!data.errcode) {
          onLoaded(data);
          showLoading(false);
        } else {
          showLoading(false);
          onLoadError(data);
        }
      }

      function onAjaxError(xhr, status){
        showLoading(false);
        var errinfo = { errcode: status }
        if (xhr.status != 200) {
          // {{--может быть статус 200, а ошибка
          // из-за некорректного JSON--}}
          errinfo.message = xhr.statusText;
        } else {
          errinfo.message = 'Некорректные данные с сервера';
        }
        onLoadError(errinfo);
      }

      function onLoaded(data) {
        //var i = 0;
        //i = data.length;
        $.each(data, function(key, val) {
          var child = JSON.parse(val);
          if(node.id == child.head) {
            var li = document.createElement('li');
            li.id = child.id;

            if(child.isFolder == 1) { li.className = "list-group-item Node Expand" + 'Closed';
              li.innerHTML = '<div class="fa fa-plus-square-o fa-lg Expand"></div><div class="Content">'+child.title+'</div>'
            } else {
              li.className = "list-group-item Node Expand" + 'Leaf';
              li.innerHTML = '<div class="fa fa-minus fa-lg Expand"></div><div class="Content">'+child.title+'</div>'
            };
            //if (key == i-1) { li.className += ' IsLast' };

            //if (child.isFolder) {
            li.innerHTML += '<ul class="list-group Container"></ul>';
            //};
            node.getElementsByTagName('ul')[0].appendChild(li);
          } else {
            var elem = document.getElementById(child.head);
            var li = document.createElement('li');
            li.id = child.id;

            if(child.isFolder == 1) { li.className = "list-group-item Node Expand" + 'Closed';
              li.innerHTML = '<div class="fa fa-plus-square-o fa-lg Expand"></div><div class="Content">'+child.title+'</div>'
            } else {
              li.className = "list-group-item Node Expand" + 'Leaf';
              li.innerHTML = '<div class="fa fa-minus fa-lg Expand"></div><div class="Content">'+child.title+'</div>'
            };
            //if (key == i-1) { li.className += ' IsLast' };

            li.innerHTML += '<ul class="list-group Container"></ul>';

            elem.getElementsByClassName('Container')[0].appendChild(li);
          }
        });

        node.isLoaded = true;
        toggleNode(node);
      }

      function onLoadError(error) {
        var msg = "Ошибка "+error.errcode;
        if (error.message) msg = msg + ' :'+error.message;
        alert(msg);
      }

      showLoading(true);

      $.ajax({
        url: url,
        type: 'GET',
        data: {'id': node.id},
        dataType: 'JSON',
        success: onSuccess,
        error: onAjaxError,
        cache: false
      });
    };

    element.onclick = function(event) {
      event = event || window.event;
      var clickedElem = event.target || event.srcElement;

      if (!hasClass(clickedElem, 'Expand')) {
        return; // {{--клик не там--}}
      };

      //{{--Node, на который кликнули--}}
      var node = clickedElem.parentNode;

      if (hasClass(node, 'ExpandLeaf')) {
        return; //{{--клик на листе--}}
      };

      if (node.isLoaded || node.getElementsByTagName('li').length) {
        //{{--Узел уже загружен через AJAX(возможно он пуст)--}}
        toggleNode(node);
        return;
      };

      if (node.getElementsByTagName('li').length) {
        //{{-- Узел не был загружен при помощи AJAX, но у него почему-то есть потомки
        // Например, эти узлы были в DOM дерева до вызова tree()
        // Как правило, это "структурные" узлы
        // ничего подгружать не надо--}}
        toggleNode(node);
        return;
      };

        //{{--загрузить узел--}}
      load(node);

    };
  });
</script>
@endpush
