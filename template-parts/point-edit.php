<form action="" method="post" enctype="multipart/form-data">
    <div class="marker-popup__title">
        <div class="service-btn-container">
            <a href="#" class="listen-btn">
            </a>
            <a href="#" class="ua-btn">UA</a>
            <div class="upload-popup upload-popup--audio">
                <label for="upload-autio">Файл з розширенням .mp3</label>
                <input type="file" id="upload-autio" name="audio" accept=".mp3">
            </div>
        </div>
        <div class="ua">
            <input type="text" class="title-field" name="title-ua" placeholder="Заголовок">
            <input type="text" class="subtitle-field" name="subtitle-ua" placeholder="Підзаголовок">
            <div class="lat-lng">
                <div>
                    <label>Довгота</label>
                    <input type="text" name="lat" placeholder="Довгота" required>
                </div>
                <div>
                    <label>Широта</label>
                    <input type="text" name="lng" placeholder="Широта" required>
                </div>
            </div>
            <div class="to-publish">
                <label for="to-publish">
                    <input type="checkbox" name="publish" id="to-publish">
                    Запропонувати до публікації
                </label>
            </div>
        </div>
        <div class="panorama-and-3d-model-upload-btn-container">
            <a href="#" class="btn-3d">
            </a>
            <a href="#" class="btn-panorama">
            </a>
            <div class="upload-popup upload-popup--3dmodel">
                <label for="obj-3d-model">Файли з розширенням .obj</label>
                <input type="file" id="obj-3d-model" name="obj-3d-model" accept=".obj">
                <label for="mtl-3d-model">Файли з розширенням .mtl</label>
                <input type="file" id="mtl-3d-model" name="mtl-3d-model" accept=".mtl">
                <label for="texture-3d-model">Текстура моделі (.jpg, .png)</label>
                <input type="file" id="texture-3d-model" name="texture-3d-model" accept="image/jpeg,image/png">
            </div>
            <div class="upload-popup upload-popup--panorama-photo">
                <label for="upload-jpg">Файл з розширенням .jpg</label>
                <input type="file" name="panorama-photo" id="upload-jpg" accept=".jpg">
            </div>
        </div>
    </div>
    <div class="marker-popup__content">
        <div class="marker-popup__description">
            <div class="photo">
                <img src="#" alt="Фото">
                <div>
                    <label for="main-thumbnail">Завантажити фото</label>
                    <input type="file" id="main-thumbnail" name="main-thumbnail" accept="image/*">
                </div>
            </div>
            <div class="text ua">
                <textarea name="description-ua" placeholder="Опис..."></textarea>
            </div>
        </div>
        <div class="marker-popup__information">
            <div class="marker-popup__information__title">
                <h3 class="ua">Пам’яткоохоронна інформація</h3>
            </div>
            <div class="marker-popup__information__description">
                <div class="photo">
                    <img src="#" alt="Фото">
                    <div>
                        <label for="monument-information-thumbnail">Завантажити фото</label>
                        <input type="file" id="monument-information-thumbnail" name="monument-information-thumbnail"
                               accept="image/*">
                    </div>
                    <div>
                        <label for="monument-information-file">Завантажити файл в форматі .pdf</label>
                        <input type="file" id="monument-information-file" name="monument-information-file"
                               accept=".pdf">
                    </div>
                </div>
                <div class="text ua">
                    <textarea name="monument-information-description-ua" placeholder="Опис..."></textarea>
                </div>
                <div class="text en">

                    <div class="mini-map"></div>
                </div>
            </div>
        </div>
        <div class="marker-popup__publications">
            <div class="marker-popup__publications__title">
                <h3 class="ua">Наукові публікації</h3>
            </div>
            <div class="marker-popup__publications__list">
                <a href="#" class="add-more-publication"></a>
                <div class="publication">
                    <div class="publication__thumbnail photo">
                        <img src="#" alt="photo">
                        <div>
                            <label for="publication-1-photo">Завантажити фото</label>
                            <input type="file" id="publication-1-photo" class="publication-photo"
                                   name="publication-1-photo"
                                   accept="image/*">
                        </div>
                        <div>
                            <label for="publication-1-file">Завантажити файл в форматі .pdf</label>
                            <input type="file" id="publication-1-file" class="publication-file"
                                   name="publication-1-file"
                                   accept=".pdf">
                        </div>
                    </div>
                    <div class="publication__description ua">
                        <textarea name="publication-1-description-ua" placeholder="Опис..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="marker-popup__media">
            <div>
                <div class="gallery">
                    <div class="gallery__title">
                        <div>
                            <h3 class="ua">Фотогалерея</h3>
                        </div>
                    </div>
                    <div class="gallery__photos">
                        <div class="upload-gallery-photo-item">
                            <a href="#" class="remove-gallery-photo"></a>
                            <label for="gallery-photo-1"></label>
                            <input type="file" id="gallery-photo-1" class="upload-gallery-photo" accept="image/*">
                        </div>
                        <a href="#" class="add-gallery-photo"></a>
                    </div>
                </div>
            </div>
            <div>
                <div class="video">
                    <div class="video__title">
                        <h4 class="ua">Відео</h4>

                    </div>
                    <div class="video__list">
                        <div class="link-to-video-item">
                            <a href="#" class="remove-link-to-video"></a>
                            <input type="text" id="link-to-video-1" class="link-to-video"
                                   placeholder="Посилання на відео з youtube">
                        </div>
                        <a href="#" class="add-link-to-video"></a>
                    </div>
                </div>
                <div class="other-resources">
                    <div class="other-resources__title">
                        <h4 class="ua">До теми</h4>

                    </div>
                    <div class="other-resources__sources">
                        <div class="other-resources-upload-item">
                            <a href="#" class="remove-other-source"></a>
                            <label for="other-resource-image-1"></label>
                            <input type="file" id="other-resource-image-1" class="other-source-image-uploader"
                                   accept="image/*">
                        </div>
                        <a href="#" class="add-other-source"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="marker-popup__edit-btn">
        <button type="submit" class="save-point-btn" id="create-new-point">
            <span class="ua">Зберегти</span>

        </button>
        <a href="#" class="close-add-point-popup">
            <span class="ua">Закрити</span>

        </a>
    </div>
</form>