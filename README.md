   ```
   $user = new \App\Models\User; // Adjust the namespace if necessary
   $user->name = 'admin';
   $user->email = 'admin@example.com'; // Use a valid email address
   $user->password = bcrypt('213eujfuir3edsjfsDD');
   $user->save();

   $user = new \App\Models\User; // Adjust the namespace if necessary
   $user->name = 'denis';
   $user->email = 'denis@denis.com'; // Use a valid email address
   $user->password = bcrypt('213eujfuir@@@dsjfsDD');
   $user->save();

```

```
+ промежуточная функция определения есть ли запрос цены true false 
+ добавлена ссылка на реплай

- реплай ссылка когда данных достаточно

- емейл аккаунты в базе
- пользователи
- админ свой интерфейс
+ пользователь может зайти по ссылке и промотреть входящее и предлагаемое письмо, отредактировать если нужно и отправить
- добавлять с СС пользователя кому пришло

- добавить ответы в базу со статусами - новое, отвечено

```


```
name: Auto Deploy on Push

on:
  push:
    branches:
      - main  # Adjust this if your deployment branch is different

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
    - name: Checkout the repository
      uses: actions/checkout@v3

    - name: Install SSH key for server access
      uses: webfactory/ssh-agent@v0.5.3
      with:
        ssh-private-key: ${{ secrets.SSH_PRIVATE_KEY }}

    - name: Pull latest code from GitHub on the server and restart services
      run: |
        ssh -o StrictHostKeyChecking=no ubuntu@148.113.138.32 << 'EOF'
          cd /var/www/ewsa.cheaptools.club
          echo "Pulling latest changes from GitHub..."
          git pull
          echo "Updating ownership of files..."
          sudo chown -R www-data:www-data /var/www/ewsa.cheaptools.club
        EOF

    - name: Send Telegram notification
      run: |
        curl -s -X POST https://api.telegram.org/bot${{ secrets.TELEGRAM_BOT_TOKEN }}/sendMessage \
        -d chat_id=-4188687896 \
        -d text="🚀 The latest EWSA-EMAIL-HELPER pulled to server!"

```