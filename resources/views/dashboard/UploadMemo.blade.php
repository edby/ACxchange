@extends('dashboard.layout.app')
@section('content')
<section class="content">    
    <div class="upload_memo">
        <div class="title">Upload Memo</div>
        <div class="from-box">
            <form class="memo-form">
                <div class="form-g">
                    <span>
                        <i class="iconfont icon-jiufuqianbaoicon14 form-iconxing"></i>
                        <label for="Title" class="form-label">Title</label>
                    </span>
                    <input type="text" class="form-input" id="Title" name="" placeholder="Title" />
                </div>
                <div class="form-g">
                    <div class="isshow">
                        <span>Visibility</span> 
                        <span>
                            <input type="radio" name="visibility" value="1" id="Visibile" checked><label for="Visibile">Visibile</label>
                            <input type="radio" name="visibility" value="2" id="Hidden"><label for="Hidden">Hidden</label>
                        </span>
                    </div>
                    <div class="browse_box">
                        <input type="text" class="form-input" id="" name="" placeholder="" />
                        <button class="browse_btn">BROWSE</button>
                    </div>        
                </div>
                <div class="form-g">
                    <input type="submit" class="btn-sub" value="UPLOAD" />
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
