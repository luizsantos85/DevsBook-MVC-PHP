<?php

namespace src\handlers;

use \src\models\Post;
use \src\models\PostLike;
use \src\models\PostComment;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler
{

  public function _postListToObject($postList, $loggedUserId)
  {
    $posts = [];
    foreach ($postList as $postItem) {
      $newPost = new Post();
      $newPost->id = $postItem['id'];
      $newPost->type = $postItem['type'];
      $newPost->created_at = $postItem['created_at'];
      $newPost->body = $postItem['body'];
      $newPost->mine = false;

      if ($postItem['id_user'] === $loggedUserId) {
        $newPost->mine = true;
      }

      //preencher as informações adicionais nos posts
      $newUser = User::select()->where('id', $postItem['id_user'])->one();
      $newPost->user = new User();
      $newPost->user->id = $newUser['id'];
      $newPost->user->name = $newUser['name'];
      $newPost->user->avatar = $newUser['avatar'];

      //preencher informações de Like (não realizado)
      $likes = PostLike::select()->where('id_post', $postItem['id'])->get();
      $newPost->likeCount = count($likes);
      $newPost->liked = self::isLiked($postItem['id'], $loggedUserId);

      //preencher informações de Comments (não realizado)
      $newPost->comments = PostComment::select()->where('id_post', $postItem['id'])->get();
      foreach ($newPost->comments as $key => $comment) {
        $newPost->comments[$key]['user'] = User::select()->where('id', $comment['id_user'])->one();
      }

      $posts[] = $newPost;
    }
    return $posts;
  }

  public static function isLiked($id, $loggedUserId)
  {
    $myLike = PostLike::select()->where('id_post', $id)->where('id_user', $loggedUserId)->get();

    if (count($myLike) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public static function addLike($id, $loggedUserId)
  {
    PostLike::insert([
      'id_post' => $id,
      'id_user' => $loggedUserId,
      'created_at' => date('Y-m-d H:i:s')
    ])->execute();
  }

  public static function deleteLike($id, $loggedUserId)
  {
    PostLike::delete()->where('id_post', $id)->where('id_user', $loggedUserId)->execute();
  }

  public static function addComment($id, $txt, $loggedUserId)
  {
    PostComment::insert([
      'id_post' => $id,
      'id_user' => $loggedUserId,
      'created_at' => date('Y-m-d H:i:s'),
      'body' => $txt,
    ])->execute();
  }

  public static function addPost($idUser, $type, $body)
  {
    $body = trim($body);

    if (!empty($idUser) && !empty($body)) {
      Post::insert([
        'id_user' => $idUser,
        'type' => $type,
        'created_at' => date('Y-m-d H:i:s'),
        'body' => $body
      ])->execute();
    }
  }

  public static function getHomeFeed($idUser, $page)
  {
    $perPage = 5;
    //Lista de usuario que sigo.
    $userList = UserRelation::select()->where('user_from', $idUser)->get();
    $users = [];
    foreach ($userList as $userItem) {
      $users[] = $userItem['user_to'];
    }
    $users[] = $idUser;

    //Posts ordenados por data
    $postList = Post::select()
      ->where('id_user', 'in', $users)
      ->orderBy('created_at', 'desc')
      ->page($page, $perPage) //paginacao
      ->get();

    //total de paginas
    $total = Post::select()
      ->where('id_user', 'in', $users)
      ->count();
    $pageCount = ceil($total / $perPage);

    //transformar os objetos dos models
    $posts = self::_postListToObject($postList, $idUser);

    //retornar o resultado
    return [
      'posts' => $posts,
      'pageCount' => $pageCount,
      'currentPage' => $page
    ];
  }

  public static function getUserFeed($idUser, $page, $loggedUserId)
  {
    $perPage = 4;

    //Posts ordenados por data
    $postList = Post::select()
      ->where('id_user', $idUser)
      ->orderBy('created_at', 'desc')
      ->page($page, $perPage) //paginacao
      ->get();

    //total de paginas
    $total = Post::select()
      ->where('id_user', $idUser)
      ->count();
    $pageCount = ceil($total / $perPage);

    //transformar os objetos dos models
    $posts = self::_postListToObject($postList, $loggedUserId);

    //retornar o resultado
    return [
      'posts' => $posts,
      'pageCount' => $pageCount,
      'currentPage' => $page
    ];
  }

  public static function getPhotosFrom($idUser)
  {
    $photosData = Post::select()->where('id_user', $idUser)->where('type', 'photo')->get();
    $photos = [];

    foreach ($photosData as $photo) {
      $newPost = new Post();
      $newPost->id = $photo['id'];
      $newPost->type = $photo['type'];
      $newPost->created_at = $photo['created_at'];
      $newPost->body = $photo['body'];

      $photos[] = $newPost;
    }
    return $photos;
  }

  public static function deletePost($id, $loggedUserId)
  {
    //verificar se o post existe
    $post = Post::select()->where('id', $id)->where('id_user', $loggedUserId)->get();
    if (count($post) > 0) {
      $post = $post[0];

      //deletar os likes e comments
      PostLike::delete()->where('id_post', $id)->execute();
      PostComment::delete()->where('id_post', $id)->execute();

      //deletar o post foto e arquivo
      if ($post['type'] === 'photo') {
        $img = __DIR__ . '/../../public/media/uploads/' . $post['body'];
        if (file_exists($img)) {
          unlink($img);
        }
      }
      //deletar o post
      Post::delete()->where('id', $id)->execute();
    }
  }
}
