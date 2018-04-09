# Сортировка больших файлов

### Создание файла
`php createFile.php`

### Сортировка слиянием
`php sortFile.php`

### Проверка
`php test.php`

Файлы создаются папке resources.
Некоторые настройки храняться в config.
Отсоритрованный файл будет в resources/result.txt

Скорость можно улучшить:
1) Использовать буфер при записи в файл
2) На первом этапе отсортировать сегменты методами php (например sort). Далее начинать с сегментов бОльшего размера, хотя бы 65536
3) Переписать на естественную сортировку. Она в среднем будет быстрее, но нужно больше логики чтобы определять конец отсортированных последовательностей.