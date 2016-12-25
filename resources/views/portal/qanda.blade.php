<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="/js/jquery-1.9.1.min.js"></script>
    <script src="/js/vue.js"></script>
    <link rel="stylesheet" href="/css/font-awesome.min.css">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <!-- Scripts -->

    <script>
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token(),]); ?>
    </script>
    <style>
    .result{
      display:none !important;
    }
    </style>
</head>
<body>

<header style="background:white;position:relative;border-bottom:1px #DDD solid;">
  <a href="/"><img src="/askgon_black.png"></a>
  <div class="rightlinks">
    @if(!Auth::user())
    <a href="/register">Sign Up</a>
    <a href="/login">Login</a>
    @else
    <a href="/logout">Logout</a>
    <a href="/home">My Dashboard</a>
    @endif
  </div>
</header>


<style>

header{
background: -moz-linear-gradient(-45deg, #444 0%, #444 99%); /* FF3.6-15 */
background: -webkit-linear-gradient(-45deg, #444 0%,#444 99%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(135deg, #444 0%,#444 99%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444', endColorstr='#444',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
}
footer{
  display:none;
}
.loading{
  display:none;
}
.rightlinks > a{
  color:black !important;
}
</style>
<section name="admin">
Pass the link to any students to access the class. Password: <span>{{$section->password}}</span>
</section>
<section name="qanda" id="qanda">
  <div class="questions">
    <div class="mainbar">
      <div class="button" id="add"><i class="fa fa-plus"></i> Add</div>
    </div>

    <div class="question" v-for="post in posts" v-on:click="choose(post)" v-bind:class="{ 'chosen': chosen.id == post.id }">
      <h1>@{{post.title}}</h1>
      <h2>@{{post.content.substring(0,100)}}</h2>
      <h3><span v-if="post.solved == true"><i class="fa fa-certificate"></i> Solved</span> &nbsp <i class="fa fa-comment"></i> @{{post.count}}</h3>
    </div>
  </div>

  <div class="main">
    <div class="pane">
      <h1>Post to the Q and A Portal</h1>
      <h2>Make a new post by filling out the following form. Your post will be kept anonymous unless you choose to
      make your identity visible for it!  </h2>
      <form action="/class/{{$section->id}}/post" method="POST">
        {!! csrf_field() !!}
        <input placeholder="Title of Post" name="title">
        <textarea placeholder="Title of Post" name="content"></textarea>
        <div class="result" style="display:none;"></div>
        <div class="row">
        <input type="checkbox" name="question" id="question" value="true"><label for="question" style="margin-top: 8px;
    float: left;"> This is a Question</label>
        <input type="submit" value="POST TO PUBLIC">
      </div>
      </form>
    </div>

    <h1>@{{chosen.title}} <span v-if="chosen.owner == {{$user->id}}" v-on:click="remove()" style="padding-top: 3px;"><i class="fa fa-trash"></i></span></h1>

    <p class="content"></p>
    <div class="correct" v-if="chosen.owner == {{$user->id}} && chosen.solved != true && chosen.question == true" v-on:click="markassolved()"
      v-bind:class="{ 'chosen': chosen.solved == true }">
      <span>Mark As Solved</span>
    </div>
    <div class="correct" v-if="chosen.owner == {{$user->id}} && chosen.solved == true && chosen.question == true" v-on:click="notsolved()"
      v-bind:class="{ 'chosen': chosen.solved == true }">
      <span><i class="fa fa-close"></i> Solved</span>
    </div>
    <br><br>
    <div class="answer">
      <div class="image addanswer" v-on:click="answer()"><i class="fa fa-plus"></i></div>
      <div class="author">ANSWER BY YOU</div>
      <form>
        {!! csrf_field() !!}
        <textarea placeholder="Title of Post" name="content" class="answer_content"></textarea>
        <div class="result answer_popup" style="display:none;"></div>
      </form>

    </div>
    <div class="answer" v-for="answer in answers">
      <div style="background-image:url('/profile.png');" class="image"></div>
      <div class="author">Answered by @{{answer.name}}
        <span v-if="answer.owner == {{$user->id}}" v-on:click="deleteanswer(answer.id)" style="cursor:pointer;"> <i class="fa fa-trash"></i>
        </span>
      </div>
      <div class="stars" v-on:click="vote(answer.id)" v-if="answer.voted != true" style="cursor:pointer;"><i class="fa fa-star"></i> @{{answer.vote}}</div>
      <div class="stars" v-else style="background-color:gold;color:white;"><i class="fa fa-star"></i> @{{answer.vote}}</div>

      <p>@{{answer.content}}</p>


            <div class="answer" v-for="subanswer in answer.subanswers" style="margin-bottom:2px;marign-top:2px;">
              <div style="background-image:url('/profile.png');" class="image"></div>
              <div class="author" style="left:55px;">Answered by @{{subanswer.name}}
                <span v-if="subanswer.owner == {{$user->id}}" v-on:click="deletesubanswer(answer.id,subanswer.id)" style="cursor:pointer;"> <i class="fa fa-trash"></i>
                </span>
              </div>
              <div class="stars" v-on:click="vote(subanswer.id, answer.id)" style="cursor:pointer;" v-if="subanswer.voted != true"><i class="fa fa-star"></i> @{{subanswer.vote}}</div>
              <div class="stars" v-else style="background-color:gold;color:white;"><i class="fa fa-star"></i> @{{subanswer.vote}}</div>

              <p>@{{subanswer.content}}</p>
            </div>
            <div class="answer">
              <div class="image addanswer" v-on:click="subanswer(answer.id)"><i class="fa fa-plus"></i></div>
              <div class="author" style="left:55px;">ANSWER BY YOU</div>
              <form>
                {!! csrf_field() !!}
                <textarea placeholder="Title of Post" name="content" v-model="answer.response" class="answer_content"></textarea>
                <div class="result answer_popup" style="display:none !important;"></div>
              </form>

            </div>
    </div>



  </div>
</section>
<script>

function format(str){
  return str;
}

$( document ).ready(function() {
  $(".pane").hide();
  $(".answer_content").focus(function(){
    $(".answer_popup").show();
    $(this).css("height","20vh");
  });
  $(document).on("click",".question",function(){
    $(".pane").hide();
    $("#add").removeClass("chosen");
  });
  $("#add").click(function(){
    $(this).toggleClass("chosen");
    $(".main").show();

    $(".pane").toggle();
  });

  $(document).on("keyup","textarea",function(){
    $(".startpanel").fadeOut();
    var str = $(this).val().replace(/\n/g, '<br/>');
    str = format(str);
    $(this).next().html(str);
  });
});

//VUE
var tours = new Vue({
  el: '#qanda',
  data: {
    posts: {!!json_encode($posts)!!},
    chosen: '',
    answers: ''
  },
  created: function () {
    //this.chosen = this.notes[0];
    //console.log(this.notes);
    console.log(this.posts);

  },
  methods: {
    choose: function (post) {
      console.log(post);
      $(".main").show();

      tobechosen = post;
      if( typeof tobechosen.answers === 'string' ) {
      tobechosen.answers = JSON.parse(tobechosen.answers);
      }
      this.answers = tobechosen.answers;
      for(i = 0; i < this.answers.length; i++){
        this.answers[i].content = format(this.answers[i].content);
        if( typeof this.answers[i].subanswers === 'string' ) {
        this.answers[i].subanswers = JSON.parse(this.answers[i].subanswers);
        }
        for(p = 0; p < this.answers[i].subanswers.length; p++){
          this.answers[i].subanswers[p].content = format(this.answers[i].subanswers[p].content);
        }
      }
      str = tobechosen.content;
      str = format(str);
      this.chosen = tobechosen;
      $(".main .content").html(str);
    },
    answer: function () {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      content = $(".answer_content").val();
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          async: false,
          url: base_url + "/class/" + {{$section->id}} + "/answerit",
          data: {'content': content, 'question': this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");
            window.answer = data;
          },
          error: function (jqXHR, json) {
              for (var error in json.errors) {
                  console.log(json.errors[error]);
              }
          },
          finished: function(data){
          }
      });

      this.chosen.answers.push(window.answer);
      this.answers = this.chosen.answers;
      $(".answer_content").val("");
      $(".answer_content").css("height","10vh");
      $(".answer_content").next().html("");
      $(".answer_popup").hide();

    },
    subanswer: function (id) {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      for(i = 0; i < this.answers.length; i++){
        if(this.answers[i].id == id){
          content = this.answers[i].response;
        }
      }
      console.log("Content: " + content);
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          async: false,
          url: base_url + "/class/" + {{$section->id}} + "/subanswer",
          data: {'content': content, 'head': id},
          success: function(data){
            window.answer = data;
          },
          finished: function(data){
          }
      });

      for(i = 0; i < this.chosen.answers.length; i++){
        if(this.chosen.answers[i].id == id){
          window.answer.content = format(window.answer.content);
          this.chosen.answers[i].subanswers.push(window.answer);
        }
      }
      this.answers = this.chosen.answers;
      $(".answer_content").val("");
      $(".answer_content").next().html("");
    },
    deleteanswer: function (id) {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          async: false,
          url: base_url + "/class/" + {{$section->id}} + "/deleteanswer",
          data: {'id': id, 'question': this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");

          },
          finished: function(data){
          }
      });
      for(i = 0; i < this.chosen.answers.length; i++){
        if(this.chosen.answers[i].id == id){
          this.chosen.answers.splice(i, 1);
        }
      }
      this.answers = this.chosen.answers;
    },
    vote: function (id,up_id) {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          async: false,
          url: base_url + "/class/" + {{$section->id}} + "/vote",
          data: {'id': id, 'question': this.chosen.id},
          success: function(data){

          },
          finished: function(data){
          }
      });
      if(up_id > 0){
        for(i = 0; i < this.chosen.answers.length; i++){
          if(this.chosen.answers[i].id == up_id){
            for(j = 0; j < this.chosen.answers[i].subanswers.length; j++){
              if(this.chosen.answers[i].subanswers[j].id == id){
                this.chosen.answers[i].subanswers[j].voted = true;
                this.chosen.answers[i].subanswers[j].vote++;
              }
            }
          }
        }
      } else {
      for(i = 0; i < this.chosen.answers.length; i++){
        if(this.chosen.answers[i].id == id){
          this.chosen.answers[i].voted = true;
          this.chosen.answers[i].vote++;
        }
      }
      }
      this.answers = this.chosen.answers;
    },
    deletesubanswer: function (id,sub_id) {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          async: false,
          url: base_url + "/class/" + {{$section->id}} + "/deleteanswer",
          data: {'id': sub_id, 'question': this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");

          },
          finished: function(data){
          }
      });
      for(i = 0; i < this.chosen.answers.length; i++){
        if(this.chosen.answers[i].id == id){
          for(j = 0; j < this.chosen.answers[i].subanswers.length; j++){
            if(this.chosen.answers[i].subanswers[j].id == sub_id){
              this.chosen.answers[i].subanswers.splice(j, 1);
            }
          }
        }
      }
      this.answers = this.chosen.answers;
    },
    markassolved: function () {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          url: base_url + "/class/" + {{$section->id}} + "/markassolved",
          data: {"question":this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");
          }
      });
      this.chosen.solved = true;
    },
    notsolved: function () {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          url: base_url + "/class/" + {{$section->id}} + "/notsolved",
          data: {"question":this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");
          }
      });
      this.chosen.solved = false;
    },
    remove: function () {
      var base_url = window.location.protocol + "//" + window.location.host;
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : "{{ csrf_token() }}"}
      });
      $.ajax({
          type: "POST", // or GET
          dataType: 'JSON',
          url: base_url + "/class/" + {{$section->id}} + "/remove",
          data: {"question":this.chosen.id},
          success: function(data){
            console.log(data);
            console.log("Done!");
          }
      });
      window.location.reload();
    }
  }
});
</script>
<!-- Scripts -->
<script src="/js/app.js"></script>
</body>
</html>