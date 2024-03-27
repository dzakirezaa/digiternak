<?php
public function actionUploadImage($id)
    {
        // Temukan model Livestock berdasarkan ID
        $model = $this->findModel($id);

        // Ambil gambar dari request
        $imageFiles = UploadedFile::getInstancesByName('livestock_image');

        // Log untuk memeriksa apakah imageFiles berisi file yang diunggah
        Yii::info('Number of uploaded files: ' . count($imageFiles));

        if ($imageFiles !== null) {
            // Ambil person_id dari pengguna yang sedang login
            $personId = Yii::$app->user->identity->person_id;

            // Buat path direktori berdasarkan person_id dan id Livestock
            $uploadPath = 'uploads/livestock/' . $personId . '/' . $model->id . '/';

            // Log untuk memeriksa path direktori
            Yii::info('Upload path: ' . $uploadPath);

            // Periksa apakah direktori sudah ada, jika tidak, buat direktori baru
            if (!is_dir($uploadPath)) {
                FileHelper::createDirectory($uploadPath);
            }

            $uploadedImages = [];

            // Iterasi melalui setiap file yang diunggah
            foreach ($imageFiles as $imageFile) {
                // Log untuk memeriksa nama file
                Yii::info('Uploaded file name: ' . $imageFile->name);

                // Generate nama file yang unik
                $imageName = Yii::$app->security->generateRandomString(12) . '.' . $imageFile->getExtension();

                // Simpan file ke direktori
                $imageFile->saveAs($uploadPath . $imageName);

                // Log untuk memeriksa path file yang disimpan
                Yii::info('Saved image path: ' . $uploadPath . $imageName);

                // Simpan informasi gambar ke dalam tabel livestock_images
                $livestockImage = new LivestockImage();
                $livestockImage->livestock_id = $model->id;
                $livestockImage->image_path = $uploadPath . $imageName;
                if (!$livestockImage->save()) {
                    throw new ServerErrorHttpException('Failed to save the image to the database');
                }

                // Simpan nama file ke dalam array
                $uploadedImages[] = $uploadPath . $imageName;
            }

            // Jika penyimpanan model berhasil
            return [
                'message' => 'Images uploaded successfully',
                'error' => false,
                'data' => [
                    'livestock_images' => $uploadedImages,
                ],
            ];
        } else {
            return [
                'message' => 'No images uploaded',
                'error' => true,
            ];
        }
    }