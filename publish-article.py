#!/usr/bin/env python3
"""
Publish an article to fmcg.ie via the WordPress REST API.

Credentials are read from environment variables:
    WP_URL          WordPress site URL (default: https://fmcg.ie)
    WP_USERNAME     WordPress username
    WP_APP_PASSWORD WordPress application password

Usage:
    python3 publish-article.py
"""

import os
import urllib.request
import urllib.error
import json
import base64
import sys

WP_URL = os.environ.get("WP_URL", "https://fmcg.ie").rstrip("/")
USERNAME = os.environ.get("WP_USERNAME", "admin")
APP_PASSWORD = os.environ.get("WP_APP_PASSWORD", "")

ARTICLE_TITLE = "The Rise of Private Label Brands in Irish Supermarkets"

ARTICLE_CONTENT = """<!-- wp:paragraph -->
<p>Over the past decade, private label brands — products manufactured for and sold under a supermarket's own name — have undergone a quiet revolution in Ireland. Once associated with no-frills packaging and budget-conscious shoppers, own-brand ranges have emerged as genuine competitors to household name manufacturers, reshaping how Irish consumers think about value, quality, and loyalty.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>From Budget Staple to Premium Choice</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tesco Ireland, SuperValu, Lidl, and Aldi have each invested heavily in elevating their own-brand portfolios. Lidl's <em>Deluxe</em> range and Aldi's <em>Specially Selected</em> line are now routinely stocked by consumers who might otherwise reach for a branded alternative. SuperValu's <em>Signature Tastes</em> range has won multiple Great Taste Awards, signalling that quality parity with premium brands is no longer aspirational — it is expected.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>The Cost-of-Living Catalyst</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ireland's cost-of-living pressures since 2022 have accelerated this trend significantly. Research by Kantar Worldpanel shows that private label products now account for over 37% of grocery spend in Irish supermarkets — a figure that continues to grow. Shoppers who traded down during the inflationary squeeze have largely stayed loyal to own-brand products, even as branded goods promotions have intensified. The value proposition has proven sticky.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>A Branding Challenge for Manufacturers</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>For FMCG brand owners, the private label surge presents both a threat and a mirror. Category captains and long-standing market leaders must now compete on more than heritage. Consumers are increasingly willing to scrutinise ingredient lists, compare unit prices, and share findings on social media — creating an environment where authenticity and tangible product superiority matter more than advertising spend alone.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Manufacturers who supply private label product alongside their branded range walk a careful strategic line, risking cannibalisation of margin while maintaining factory utilisation. Those investing in genuine innovation, sustainability credentials, and brand storytelling are finding the strongest defence against the own-brand tide.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>What's Next for Own-Brand in Ireland?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The next frontier for Irish private label is premiumisation and category expansion. Fresh, chilled, and prepared-meal segments are seeing rapid own-brand growth, as are health, free-from, and plant-based categories. Retailers are also experimenting with tiered architectures — entry-level value lines sitting alongside mid-tier and premium own-brand tiers — giving shoppers a curated range within a single brand family.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>For brand builders operating in Irish FMCG, understanding the private label landscape is no longer optional. It is central to pricing strategy, innovation pipeline, and retailer negotiation. The rise of own-brand is not a temporary shift — it is a structural realignment of how Irish shoppers define value.</p>
<!-- /wp:paragraph -->"""

ARTICLE_EXCERPT = (
    "Private label brands have transformed from budget staples to premium competitors "
    "in Irish supermarkets. We examine the forces driving own-brand growth and what it "
    "means for FMCG manufacturers."
)

CATEGORY_SLUG = "branding"


def get_auth_header():
    creds = base64.b64encode(f"{USERNAME}:{APP_PASSWORD}".encode()).decode()
    return {"Authorization": f"Basic {creds}"}


def api_url(path):
    # Use ?rest_route= fallback format, merging any path query params as &param=value
    if "?" in path:
        route, qs = path.split("?", 1)
        return f"{WP_URL}/?rest_route=/wp/v2{route}&{qs}"
    return f"{WP_URL}/?rest_route=/wp/v2{path}"


def api_get(path):
    url = api_url(path)
    req = urllib.request.Request(url, headers={**get_auth_header(), "Accept": "application/json"})
    with urllib.request.urlopen(req) as r:
        return json.loads(r.read().decode())


def api_post(path, payload):
    url = api_url(path)
    data = json.dumps(payload).encode()
    req = urllib.request.Request(
        url,
        data=data,
        headers={**get_auth_header(), "Content-Type": "application/json", "Accept": "application/json"},
        method="POST",
    )
    with urllib.request.urlopen(req) as r:
        return json.loads(r.read().decode())


def find_or_create_category(slug, name):
    cats = api_get(f"/categories?slug={slug}&per_page=1")
    if cats:
        cat_id = cats[0]["id"]
        print(f"Found category '{name}' with ID {cat_id}")
        return cat_id
    # Category not found — create it
    cat = api_post("/categories", {"name": name, "slug": slug})
    print(f"Created category '{name}' with ID {cat['id']}")
    return cat["id"]


def publish():
    print("Connecting to WordPress REST API...")

    cat_id = find_or_create_category(CATEGORY_SLUG, "Branding")

    print("Publishing article...")
    post = api_post(
        "/posts",
        {
            "title": ARTICLE_TITLE,
            "content": ARTICLE_CONTENT,
            "excerpt": ARTICLE_EXCERPT,
            "status": "publish",
            "categories": [cat_id],
        },
    )

    print(f"\nArticle published successfully!")
    print(f"  Title : {post['title']['rendered']}")
    print(f"  URL   : {post['link']}")
    print(f"  ID    : {post['id']}")


if __name__ == "__main__":
    if not APP_PASSWORD:
        print("Error: WP_APP_PASSWORD environment variable is not set.", file=sys.stderr)
        sys.exit(1)
    try:
        publish()
    except urllib.error.HTTPError as e:
        body = e.read().decode()
        print(f"HTTP {e.code} error: {body[:400]}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        sys.exit(1)
