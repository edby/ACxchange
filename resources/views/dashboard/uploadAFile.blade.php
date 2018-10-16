@extends('dashboard.layout.app')
@section('content')
<section class="content">
    <div class="dashboard">
        <div class="titl">Upload a File</div>
        <div class="upload">
            <div class="select">
                <div>
                    <span>Type</span>
                    <p>
                        <input type="radio" name="type" value="1" id="video" checked><label for="video">Video</label>
                        <input type="radio" name="type" value="2" id="presentation"><label for="presentation">Presentation</label>       
                    </p>
                </div>
                <div>
                        <span>visibility</span>
                        <p>
                            <input type="radio" name="visibility" value="3" id="visible" checked><label for="visible">Visible</label>
                            <input type="radio" name="visibility" value="4" id="hidden"><label for="hidden">Hidden</label>       
                        </p>
                </div>
            </div>
            <div class="text0">
                <span>File Description</span>
                <textarea>File Description</textarea>
            </div>
            <div class="browse">
                    <input type="text" class="browseinput" id="" name="" placeholder="">
                    <button class="browsebtn">BROWSE</button>
            </div>
            <div class="upload0">
                    <input type="submit" class="uploadbtn" value="UPLOAD">
                </div>
        </div>
        
    </div>
</section>
@endsection