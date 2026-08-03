<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * File Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $date_id
 * @property int $idea_id
 * @property int $song_id
 * @property int $song_version_id
 * @property string $title
 * @property string $file
 * @property array $regions
 * @property bool $is_public
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Date $date
 * @property \App\Model\Entity\Idea $idea
 * @property \App\Model\Entity\Song $song
 * @property \App\Model\Entity\SongsVersion $songVersion
 * @property \App\Model\Entity\Collection[] $collections
 * @property \App\Model\Entity\Share[] $shares
 */
class File extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];

    public function getExtension()
    {
        $fileFormat = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        return match ($fileFormat) {
            'mp3' => 'mpeg',
            'm4a' => 'mp4',
            default => $fileFormat,
        };
    }

    public function isAudio()
    {
        $fileFormat = $this->getExtension();
        return in_array($fileFormat, ['mp3', 'mpeg', 'ogg', 'wav', 'flac', 'm4a', 'mp4']);
    }

    public function isImage()
    {
        $fileFormat = $this->getExtension();
        return in_array($fileFormat, ['jpg', 'jpeg', 'png', 'gif']);
    }

    public function getType()
    {
        return $this->isAudio() ? 'audio' : ($this->isImage() ? 'image' : 'other');
    }

    public function getRelativePath()
    {
        return DS . 'uploads' . DS . $this->file;
    }

    public function getAbsolutePath()
    {
        return $path = WWW_ROOT . 'uploads' . DS . $this->file;
    }

    public function isMissing()
    {
        return file_exists($this->getAbsolutePath()) === false;
    }

    public function getDimensions()
    {
        $size = getimagesize($this->getAbsolutePath());
        if ($size) {
            return [
                'w' => $size[0],
                'h' => $size[1],
                'r' => $size[0] / $size[1],
            ];
        } else {
            throw new \Exception('Cannot get image size of: ' . $this->getAbsolutePath());
        }
    }

    public function getAudioPlayerData()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->getRelativePath(),
            'urlEdit' => Router::url(['controller' => 'Files', 'action' => 'edit', $this->id]),
            'regions' => json_decode($this->regions, true) ?? [],
        ];
    }
}
