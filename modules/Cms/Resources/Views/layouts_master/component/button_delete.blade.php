<form style="display:inline-block;" method="POST" action="{{ url($delete) }}">
  {{ method_field('DELETE') }}
  {{ csrf_field() }}
    <button class="btn btn-sm btn-danger" type="submit" title="Hapus">
        <i class="mdi mdi-trash-can"></i> Hapus
    </button>
</form>
