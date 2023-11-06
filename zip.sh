folder_path="./output"
to_path="./outputZip"

if [ -d "$folder_path" ]; then
  for file in "$folder_path"/*; do
    if [ -f "$file" ]; then
      filename=$(basename "$file")
      zip -j "$to_path/${filename%.*}.zip" "$file"
      echo "已將 $file 壓縮為 $to_path/${filename%.*}.zip"
    fi
  done
else
  echo "指定資料夾無效。"
fi
