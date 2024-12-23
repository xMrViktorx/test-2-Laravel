@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show position-absolute" style="top: 20px; right: 20px; z-index: 9999" role="alert">
        <strong>Success!</strong> {{ $message }}
        <button type="button" class="btn position-absolute p-0" style="top: -4px; right: 10px;" id="closeAlert" data-dismiss="alert" aria-label="Close">
            <span class="text-light" style="font-size: 1.5rem;">x</span>
        </button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show position-absolute" style="top: 20px; right: 20px; z-index: 9999" role="alert">
        <strong>{{ $message }}</strong>
        <button type="button" class="btn position-absolute p-0" style="top: -4px; right: 10px;" id="closeAlert" data-dismiss="alert" aria-label="Close">
            <span class="text-light" style="font-size: 1.5rem;">x</span>
        </button>
    </div>
@endif

@push('js')
    <script>
        function closeAlert(event) {
            let element = event.target;
            while (element.nodeName !== "BUTTON") {
                element = element.parentNode;
            }
            element.parentNode.parentNode.removeChild(element.parentNode);
        }

        setTimeout(function() {
            if (document.getElementById("closeAlert")) {
                document.getElementById("closeAlert").click();
            }
        }, 4000)
    </script>
@endpush