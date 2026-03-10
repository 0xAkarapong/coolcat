import csv
import random
import datetime

def generate_users(count):
    users = []
    for i in range(1, count + 1):
        users.append({
            'name': f'Fake User {i}',
            'username': f'user{i}',
            'email': f'user{i}@example.com',
            'email_verified_at': '2025-01-01 12:00:00',
            'password': 'password',
            'role': random.choice(['user', 'admin']),
            'phone': f'123-456-{i:04d}',
            'province': random.choice(['Bangkok', 'Chiang Mai', 'Phuket']),
            'is_verified': random.choice(['true', 'false']),
            'remember_token': '',
            'two_factor_secret': '',
            'two_factor_recovery_codes': '',
            'two_factor_confirmed_at': ''
        })
    with open('users_fake_data.csv', 'w', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=users[0].keys())
        writer.writeheader()
        writer.writerows(users)

def generate_cat_listings(count):
    listings = []
    for i in range(1, count + 1):
        t = random.choice(['adoption', 'sale'])
        listings.append({
            'user_id': random.randint(1, 10),
            'breed_id': random.randint(1, 5),
            'name': f'Cat {i}',
            'gender': random.choice(['male', 'female', 'unknown']),
            'birthdate': '2023-01-01',
            'color': random.choice(['white', 'black', 'orange', 'grey', 'tabby', 'calico', 'cream']),
            'description': 'A very lovely cat looking for a new home.',
            'image': '',
            'type': t,
            'price': round(random.uniform(500, 50000), 2) if t == 'sale' else '',
            'status': random.choice(['active', 'reserved', 'sold', 'closed']),
            'is_neutered': random.choice(['true', 'false']),
            'is_vaccinated': random.choice(['true', 'false']),
            'views': random.randint(0, 200),
            'province': random.choice(['Bangkok', 'Chiang Mai', 'Phuket'])
        })
    with open('cat_listings_fake_data.csv', 'w', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=listings[0].keys())
        writer.writeheader()
        writer.writerows(listings)

def generate_products(count):
    products = []
    for i in range(1, count + 1):
        products.append({
            'user_id': random.randint(1, 10),
            'name': f'Product {i}',
            'description': 'High quality cat product.',
            'category': random.choice(['food', 'toy', 'accessory', 'health', 'litter', 'grooming', 'furniture', 'other']),
            'price': round(random.uniform(50, 5000), 2),
            'stock': random.randint(0, 100),
            'image': '',
            'is_active': random.choice(['true', 'false'])
        })
    with open('products_fake_data.csv', 'w', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=products[0].keys())
        writer.writeheader()
        writer.writerows(products)

generate_users(50)
generate_cat_listings(50)
generate_products(50)
print("CSVs generated successfully.")
