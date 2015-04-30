<?php

namespace app\modules\image\models;

use app\behaviors\ImageExist;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\imagine\Image as Imagine;
use yii\web\BadRequestHttpException;


/**
 * This is the model class for table "thumbnail".
 * @property integer $id
 * @property integer $img_id
 * @property string $thumb_src
 * @property integer $size_id
 */
class Thumbnail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%thumbnail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['img_id', 'thumb_src', 'size_id'], 'required'],
            [['img_id', 'size_id'], 'integer'],
            [['thumb_src'], 'string', 'max' => 255]
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageExist::className(),
                'srcAttrName' => 'thumb_src',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'img_id' => Yii::t('app', 'Img ID'),
            'thumb_src' => Yii::t('app', 'Thumb Src'),
            'size_id' => Yii::t('app', 'Size ID'),
        ];
    }

    /**
     * Return thumb of image by size or create if not exist
     * @param $image Image
     * @param $size ThumbnailSize
     * @return static
     */
    public static function getImageThumbnailBySize($image, $size)
    {
        $thumb = static::findOne(['img_id' => $image->id, 'size_id' => $size->id]);
        if ($thumb === null) {
            $thumb = new Thumbnail;
            $thumb->setAttributes(
                [
                    'img_id' => $image->id,
                    'size_id' => $size->id,
                ]
            );
            $thumb->thumb_src = static::createThumbnail($image, $size);
            $thumb->save();
        }
        return $thumb;
    }

    /**
     * Create thumbnail in fs
     * @param $image Image
     * @param $size ThumbnailSize
     * @return string
     */
    public static function createThumbnail($image, $size)
    {
        $path = Yii::$app->getModule('image')->thumbnailsDirectory;
        $file_info = pathinfo($image->image_src);
        $src = "$path/{$file_info['filename']}-{$size->width}x{$size->height}.{$file_info['extension']}";
        if (file_exists('@webroot' . $image->image_src)) {
            $thumb = Imagine::thumbnail('@webroot' . $image->image_src, $size->width, $size->height, $size->resize_mode);
            $thumb->save(Yii::getAlias('@webroot') . $src);
        }
        return $src;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (Yii::$app->getModule('image')->useWatermark == 1) {
            $size = ThumbnailSize::findOne(ArrayHelper::getValue($this, 'size_id', 0));
            if ($size !== null) {
                $watermark = Watermark::findOne($size->default_watermark_id);
                ThumbnailWatermark::getThumbnailWatermark($this, $watermark);
            } else {
                throw new BadRequestHttpException(Yii::t('app', 'Set thumbnail size'));
            }
        }
    }
}