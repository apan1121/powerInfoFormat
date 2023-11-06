folder_path="./output" # 将此路径替换为要压缩文件的文件夹路径
to_path="./outputZip"

if [ -d "$folder_path" ]; then
  for file in "$folder_path"/*; do
    if [ -f "$file" ]; then
      filename=$(basename "$file")
      zip -j "$to_path/${filename%.*}.zip" "$file"
      echo "已将 $file 压缩为 $to_path/${filename%.*}.zip"
    fi
  done
else
  echo "指定的文件夹路径无效。"
fi
