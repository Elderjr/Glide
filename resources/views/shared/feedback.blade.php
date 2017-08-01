<div class="row">
    <div class="col-md-4 col-md-offset-4">
@if(session('feedback'))
    @if(session('feedback')->success != null)
        <div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{session('feedback')->success}}
        </div>
    @endif
    @if(session('feedback')->alert != null)
        <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{session('feedback')->alert}}
        </div>
    @endif
    @if(session('feedback')->error != null)
        <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{session('feedback')->error}}
        </div>
    @endif
@endif

@if(isset($feedback))
    @if($feedback->success != null)
        <div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{$feedback->success}}
        </div>
    @endif
    @if($feedback->alert != null)
        <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{$feedback->alert}}
        </div>
    @endif
    @if($feedback->error != null)
        <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            {{$feedback->error}}
        </div>
    @endif
@endif
    </div>
</div>