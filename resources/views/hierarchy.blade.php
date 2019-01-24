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
    @if($worker->head_id == null )
      <li id="{{ $worker->id }}" class="list-group-item Node IsRoot IsLast ExpandOpen">
        <div class="fa fa-minus-square-o fa-lg Expand"></div>
        <div class="Content">{!! '<h6>'.$worker->name_position.'</h6>'.$worker->nameWorker!!}</div>
        <ul class="list-group Container">
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
  var obj = {!! $workers !!};

  Object.keys(obj).forEach(function(key) {
    if (this[key].head_id != null) {
      var elem = document.getElementById(this[key].head_id);
      var li = document.createElement('li');
      li.id = this[key].id;

      var count = this[key].count;

      if(count >= 1) {
        li.className = "list-group-item Node Expand" + 'Closed';
        //
        li.innerHTML = '<div class="fa fa-plus-square-o fa-lg Expand"></div><div class="Content">'+'<h6>'+this[key].name_position+'</h6>'+this[key].nameWorker+'</div>';
      } else {
        li.className = "list-group-item Node Expand" + 'Leaf';
        //
        li.innerHTML = '<div class="fa fa-minus fa-lg Expand"></div><div class="Content">'+'<h6>'+this[key].name_position+'</h6>'+this[key].nameWorker+'</div>';
      };

      if (count != 0) {
        li.innerHTML += '<ul class="list-group Container"></ul>';
      }
      elem.getElementsByClassName('Container')[0].appendChild(li);
    }
  }, obj);

  $(document).ready(function() {
    $(".hierarchy").addClass('active');

    function hasClass(elem, className) {
      return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className);
    };

    function toggleNode(node) {
      // определить новый класс для узла
      var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen';
      var element = node.getElementsByTagName('div')[0];
      var newElem = hasClass(element, 'fa-minus-square-o') ? 'fa-plus-square-o' : 'fa-minus-square-o';
      // заменить текущий класс на newClass
      // находим отдельно стоящий open|close и меняет на newClass
      var re =  /(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/;
      node.className = node.className.replace(re, '$1'+newClass+'$3');
      var de =  /(^|\s)(fa-minus-square-o|fa-plus-square-o)(\s|$)/;
      element.className = element.className.replace(de, '$1'+newElem+'$3');
    };

    document.getElementById('tree').onclick = function(event) {
      event = event || window.event;
      var clickedElem = event.target || event.srcElement;

      if (!hasClass(clickedElem, 'Expand')) {
        return; // клик не там
      };

      // Node, на который кликнули
      var node = clickedElem.parentNode;

      if (hasClass(node, 'ExpandLeaf')) {
        return; // клик на листе
      };

      if (node.isLoaded || node.getElementsByTagName('li').length) {
        toggleNode(node);
        return;
      };

      if (node.getElementsByTagName('li').length) {
        toggleNode(node);
        return;
      };
    };
  });
</script>
@endpush
