<?php

namespace modules\blog\models;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * This is the model class for table "post_category".
 *
 * @property int $id
 * @property int $order
 * @property string $title
 * @property string $alias
 * @property string $description
 * @property string $keywords
 * @property string $content_img
 * @property string $content_title
 * @property string $content_desc
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Post[] $posts
 */
class PostCategory extends ActiveRecord
{

    public $imageFile;

    public static function tableName() { return 'post_category'; }
    public function behaviors() { return [ TimestampBehavior::class ]; }


    public function rules()
    {
        return [
            [['order', 'created_at', 'updated_at'], 'integer'],
            [['title', 'alias', 'content_title', 'content_desc'], 'required'],
            [['content_desc'], 'string'],
            [['title', 'alias', 'description', 'keywords', 'content_img', 'content_title'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Сортировка',
            'title' => 'Название (title)',
            'alias' => 'Псевдоним (url)',
            'description' => 'Описание страницы (meta description)',
            'keywords' => 'Ключевые слова (meta keywords)',
            'content_img' => 'Фото',
            'content_title' => 'Заголовок (h1)',
            'content_desc' => 'Описание',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }


    public function beforeSave($insert)
    {
        if ($file = UploadedFile::getInstance($this, 'imageFile')) {
            $path = dirname(Yii::getAlias('@webroot'));
            $fileName = Yii::$app->security->generateRandomString(6);
            $imgUrl = '/img/blog/' . $fileName . '.jpg';
            $file->saveAs($path . $imgUrl);
            Image::resize($path . $imgUrl, null, 600, false, true)
                ->crop(new Point(0, 0), new Box(600, 600))
                ->save($path . $imgUrl);
            $this->content_img = $imgUrl;
        }
        return parent::beforeSave($insert);
    }


    public function deleteImage()
    {
        $path = dirname(Yii::getAlias('@webroot'));
        if (file_exists($path . $this->content_img)) {
            unlink($path . $this->content_img);
        }
        $this->content_img = null;
        $this->save(false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['category_id' => 'id']);
    }

}
