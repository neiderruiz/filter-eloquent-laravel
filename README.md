[![Github](https://img.shields.io/badge/-GitHub-000?style=flat&logo=Github&logoColor=white)](https://www.tiktok.com/@neiderruiz_)
[![Github](https://img.shields.io/badge/-TikTok-000?style=flat&logo=Tiktok&logoColor=white)](https://www.tiktok.com/@neiderruiz_)
[![Linkedin](https://img.shields.io/badge/-Instagram-%23E4405F?style=flat&logo=Instagram&logoColor=white)](https://www.instagram.com/neiderruiz_/)
[![Gmail](https://img.shields.io/badge/-YouTube-FF0000?style=flat&logo=YouTube&logoColor=white)](https://www.youtube.com/@neiderruiz)

## Translate text php

```
composer require neiderruiz/filter-eloquent-laravel
```

# how using translator

```
// import pachage
use Neiderruiz\FilterEloquentLaravel\Traits\FilterQuery;

// use trait in controller
class UserController extends Controller
{
    use FilterQuery;
}

// example use filter
public function index(Request $request)
    {
        $this->filter = User::query();
        $this->addWith();
        $this->where();
        $this->search();
        return $this->success($this->paginate());
    }

// your url request
?paginate=true&search=[articles,name,laravel]&with=[articles:id,title]

// get specific fields
&inputs=id,name,email

```

## get specific fields

```php
// your url request
&fields=id,name,email
```

## with relations
    
```php
&with=[posts:id,title,user_id]
```

## with multiple relations

```php
//example one
&with=[posts:id,title,user_id]

// example two
&with=[posts:id,title,user_id][posts.comments]

// example three
&with=[posts:id,title,user_id][posts.comments][posts.comments.user]

// example four
&with=[posts:id,title,user_id][posts.comments][posts.comments:id,description]
```

## Simple Search

```php
// your url request
&search=[name,your text]
```

## Search With Relation

```php
// your url request
&search=[articles,name,your text]
```

## Search With Multiple Relation

```php
// your url request
&search=[articles,name,your text][articles,description,your text]
```

## Where

```php
// example one
&where=[id,10]

// example two
&where=[id,10][name,neider ruiz]

// example three
&where=[name,neider ruiz,like]

// example four
&where=[age,20,<=]

```

## Where With Relation

```php
// example one
&where=[articles,id,10]

// example two
&where=[articles,title,mi titulo,like]
```	
