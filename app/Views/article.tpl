<article>
    <h2>{{$article->title}}</h2>
    <img src="{{$article->image}}" width="400" height="300" alt="{{$article->title}}">
    <p>{{$article->short_text}}</p>
    <em>{{$article->created_at}}</em>
</article>