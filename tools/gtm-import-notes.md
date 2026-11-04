# GTM import — Christiane Buchmann

File: `tools/gtm-container-export.json`  
Container: `GTM-M9XXZQPF`  
GA4: `G-LW6EE8CZ1Y`

## Import

1. Tag Manager → **Admin** → **Import Container**
2. Choose `gtm-container-export.json`
3. Workspace: **New** (recommended) or Existing
4. Choose **Merge** → **Rename conflicting tags, triggers, and variables**
5. Confirm → **Submit** → **Publish**

## What it tracks

| Tag | Event | Trigger |
| --- | --- | --- |
| Google Tag – GA4 Configuration | page_view (+ traffic source/medium/campaign via GA4) | All Pages |
| GA4 – Virtual Page View | page_view | History Change |
| GA4 – Scroll | scroll | 25 / 50 / 75 / 90% |
| GA4 – Outbound Click | outbound_click | External http(s) links |
| GA4 – Generate Lead (Mailto) | generate_lead | `mailto:` |
| GA4 – Phone Click | phone_click | `tel:` |
| GA4 – Select Project | select_project | Links to `/all-projects/` |
| GA4 – Contact Nav | contact_click | Links to `/contact/` |
| GA4 – Social Click | social_click | Instagram, Vimeo, Facebook, LinkedIn, Xing |
| GA4 – Crew United Click | crew_united_click | crew-united.com |
| GA4 – File Download | file_download | pdf/doc/xls/zip/mp4/mov |
| GA4 – Form Submit | form_submit | Any form submit |
| GA4 – Video Start / Progress / Complete | video_* | dataLayer from trailer player |

Traffic source (“how visited”) is collected automatically by the Google tag (referrer + UTM).

## Mark conversions in GA4

In GA4 → **Admin** → **Events** → mark as conversions (suggested):

- `generate_lead`
- `phone_click`
- `contact_click`
- `select_project`
- `video_start` (optional engagement conversion)

## After publish

Use GTM Preview + GA4 DebugView on `christianebuchmann.com` to verify events before relying on reports.
