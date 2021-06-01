<form action="#">
    <input id="route-id-field" type="text" name="route-id" hidden>
    <div>
        <label for="route-title-field">Назва маршруту</label>
        <input type="text" id="route-title-field" name="route-title" required>
    </div>
    <div>
        <label for="route-description-field-ua">Опис маршруту Українською</label>
        <textarea id="route-description-field-ua" name="route-description-ua"></textarea>
    </div>
    <div>
        <label for="route-description-field-en">Опис маршруту Англійською</label>
        <textarea id="route-description-field-en" name="route-description-en"></textarea>
    </div>
    <div>
        <label for="route-image">
            Фото маршруту
            <div class="photo-preview"></div>
        </label>
        <input type="file" name="route-image" id="route-image" accept="image/jpeg, image/png">
    </div>
    <div>
        <label for="route-audio">Звукова доріжка</label>
        <audio src="" class="route-audio" controls></audio>
        <input type="file" name="route-audio" id="route-audio" accept="*.mp3">
    </div>
    <div>
        <label class="type-of-route">
            <input type="checkbox" name="virtual" id="virtual-route">
            Віртуальний
        </label>
        <label class="type-of-route">
            <input type="checkbox" name="real" id="real-route">
            Реальний
        </label>
        <label class="made-public">
            <input type="checkbox" name="made-public" id="made-public">
            Запропонувати до публікації
        </label>
    </div>
    <label>Список точок маршруту</label>
    <div class="routes-list">
    </div>
    <a href="#" class="add-more-point">Додати точку</a>
    <div class="buttons">
        <button type="submit" id="edit-route-submit">Редагувати</button>
        <a href="#" id="remove-route">Видалити</a>
    </div>
</form>