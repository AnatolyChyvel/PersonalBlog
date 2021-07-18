<?php

namespace MyProject\Models\Profiles;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;

class Profile extends ActiveRecordEntity
{
    /** @var int */
    protected $userId;

    /** @var string */
    protected $lastActivity;

    protected $image;

    /** @var string */
    protected $aboutMe;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getLastActivity(): string
    {
        return $this->lastActivity;
    }

    /**
     * @return string
     */
    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    /**
     * @return string
     */
    public function getSquareImage(): string
    {
        $tmpImage = imagecreatefromstring($this->image);
        $size = min(imagesx($tmpImage), imagesy($tmpImage));
        $result = imagecrop($tmpImage, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);

        ob_start();
        imagegif($result);
        $buffer = ob_get_contents();
        ob_clean();

        $base64 = base64_encode($buffer);

        imagedestroy($result);
        imagedestroy($tmpImage);

        return $base64;
    }

    public function getFullImage(): string
    {
        return base64_encode($this->image);
    }

    /**
     * @param string $aboutMe
     */
    public function setAboutMe(string $aboutMe): void
    {
        $this->aboutMe = $aboutMe;
    }

    /**
     * @param mixed $image
     */
    protected function setImage($image): void
    {
       $this->image = $image;
    }

    /**
     * @param string $lastActivity
     */
    public function updateLastActivity(): void
    {
        $this->lastActivity = date("Y-m-d H:i:s");
        $this->save();
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public static function create(User $user): void
    {
        $profile = new Profile();

        $profile->setUserId($user->getId());
        $profile->setImage(file_get_contents(__DIR__ . '/../../../Images/profile_default_image.png'));
        $profile->save();
    }

    public function updateFromArray(array $fields): Profile
    {
        $this->setAboutMe($fields['aboutMe'] ?? '');
        $this->updateLastActivity();
        $this->save();

        return $this;
    }

    public function uploadImage(array $image)
    {
        $settings = (require __DIR__ . '/../../../settings.php')['image'];
        $allowedExtensions = $settings['allowedExtensions'];

        $srcImageName = $image['name'];
        $extension = pathinfo($srcImageName, PATHINFO_EXTENSION);

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            throw new InvalidArgumentException('Загрузка файлов с таким расширением запрещена!');
        }

        if ($image['size'] > $settings['maxSize']){
            throw new InvalidArgumentException('Размер изображения должен быть не более '. $settings['maxSize']/(1024*1024)  . 'МБ.');
        }

        $this->setImage(file_get_contents($image['tmp_name']));

        $this->save();
    }

    public static function getProfileByUserId(int $userId): ?Profile
    {
        return Profile::findOneByColumn('user_id', $userId);
    }

    protected static function getTableName(): string
    {
        return 'profiles';
    }
}