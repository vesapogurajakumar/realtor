# Property & Blog Data — How to Edit

This folder holds the site's content as JSON files:

| File | What it controls |
|------|------------------|
| `properties.json` | All property listings (cards, listings page, detail pages, map) |
| `blog.json` | All blog posts |

> ⚠️ **Easiest way to add a listing:** use the **admin panel** at `/admin` → **Add Listing**.
> It writes to the database and appears on the site instantly — no JSON editing, no risk of breaking the file.
> Edit the JSON directly only if you prefer to, or to change the original sample listings.

---

## 1. Editing `properties.json`

The file looks like this:

```jsonc
{
  "_instructions": { ... },     // help text — ignored by the site, leave it
  "properties": [ {...}, {...} ],  // ← your listings go here
  "neighborhoods": [ {...} ]       // ← Home page "Featured Neighborhoods"
}
```

### To add a new listing
1. Copy one entire `{ ... }` block inside `"properties"`.
2. Paste it as a new block — **put a comma `,` between blocks**.
3. Edit the values. **Give every listing a unique `"id"`** (e.g. `prop_013`).
4. Save. It appears within ~5 minutes (data is cached), or instantly if you add via the admin panel.

### Field reference

| Field | Required | Example | Notes |
|-------|:--------:|---------|-------|
| `id` | ✅ | `"prop_013"` | Must be **unique**. Used in the page URL `/property/prop_013`. |
| `title` | ✅ | `"Lakeshore Villa"` | The listing name. |
| `type` | ✅ | `"Villa"` | Villa, Penthouse, Estate, Townhouse, Condo, Single Family… |
| `status` | ✅ | `"For Sale"` | **Exactly** `For Sale`, `For Rent`, or `Sold`. |
| `price` | ✅ | `2450000` | Number only — no `$` or commas. For rentals = monthly rent. |
| `city` | ✅ | `"Austin"` | Also powers the search city suggestions. |
| `featured` | optional | `true` | `true` = show in the Home "Featured" slider. |
| `beds` | optional | `5` | Number. |
| `baths` | optional | `4` | Number. |
| `sqft` | optional | `4200` | Number (square feet). |
| `lot_size` | optional | `"0.8 acres"` | Free text. |
| `year_built` | optional | `2019` | Number. |
| `mls` | optional | `"MLS-2024110"` | Listing/MLS number. |
| `address` | optional | `"142 Lakeshore Dr"` | Street address. |
| `state` | optional | `"TX"` | State code. |
| `zip` | optional | `"78732"` | ZIP/postal code. |
| `lat` | optional | `30.3635` | Map latitude (see tip below). |
| `lng` | optional | `-97.9911` | Map longitude. |
| `neighborhood` | optional | `"Lakeway"` | Area/community name. |
| `description` | optional | `"Perched on..."` | Full description (shown on detail page). |
| `features` | optional | `["Pool","Smart Home"]` | List of selling points (detail page). |
| `amenities` | optional | `["Pool","Garage"]` | Used by the **Listings filter sidebar** — keep wording consistent. |
| `images` | optional | `["https://.../1.jpg"]` | List of photo URLs. **First = cover image.** |
| `agent` | optional | see below | The listing agent shown on the detail page. |
| `hoa` | optional | `450` | Monthly HOA fee (number, 0 if none). |
| `garage` | optional | `3` | Number of garage spaces. |
| `walk_score` | optional | `42` | 0–100, shown on detail page. |
| `transit_score` | optional | `28` | 0–100. |
| `virtual_tour` | optional | `"https://..."` | 3D/video tour link. |
| `listed_date` | optional | `"2025-04-12"` | `YYYY-MM-DD`. Controls "Newest" sorting. |
| `pois` | optional | see template | Nearby schools/hospitals/etc. shown on the detail map. |

**Agent object:**
```json
"agent": {
  "name": "Sarah Mitchell",
  "title": "Senior Advisor",
  "phone": "+1 (512) 555-0192",
  "whatsapp": "15125550192",
  "email": "sarah@vestarealty.com",
  "photo": "https://.../agent.jpg"
}
```

💡 **Getting `lat` / `lng`:** open [Google Maps](https://maps.google.com), right-click the location → click the coordinates at the top of the menu to copy them (first number = `lat`, second = `lng`).

---

## 2. Blank template — copy & paste

Paste this inside `"properties": [ ... ]` (remember the comma between listings) and fill it in:

```json
{
  "id": "prop_013",
  "title": "",
  "type": "Villa",
  "status": "For Sale",
  "featured": false,
  "price": 0,
  "beds": 0,
  "baths": 0,
  "sqft": 0,
  "lot_size": "",
  "year_built": 2024,
  "mls": "",
  "address": "",
  "city": "",
  "state": "",
  "zip": "",
  "lat": 0,
  "lng": 0,
  "neighborhood": "",
  "description": "",
  "features": ["", ""],
  "amenities": ["Pool", "Garage"],
  "images": [
    "https://your-image-host.com/photo-1.jpg",
    "https://your-image-host.com/photo-2.jpg"
  ],
  "agent": {
    "name": "",
    "title": "Listing Advisor",
    "phone": "",
    "whatsapp": "",
    "email": "",
    "photo": ""
  },
  "hoa": 0,
  "garage": 0,
  "walk_score": 0,
  "transit_score": 0,
  "virtual_tour": "",
  "listed_date": "2025-01-01",
  "pois": [
    { "name": "Local Elementary", "type": "school",   "rating": 8, "lat": 0, "lng": 0, "distance": "0.5 mi" },
    { "name": "City Hospital",    "type": "hospital", "rating": 8, "lat": 0, "lng": 0, "distance": "2.0 mi" }
  ]
}
```

`pois` `type` must be one of: `school`, `hospital`, `grocery`, `transit`.

---

## 3. Neighborhoods (Home page grid)

```json
{
  "name": "Lakeway",
  "city": "Austin",
  "state": "TX",
  "image": "https://.../neighborhood.jpg",
  "tagline": "Waterfront living on Lake Travis"
}
```

---

## 4. Common mistakes that break the file

- **Missing comma** between two listings, or a **trailing comma** after the last one.
- Forgetting to wrap text in `"double quotes"`.
- Putting `$` or commas in `price` (use `2450000`, not `$2,450,000`).
- Duplicate `id` values.

✅ After editing, paste the whole file into <https://jsonlint.com> to confirm it's valid before saving.
```
