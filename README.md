# Wordpress Network Text Replacement Plugin

Find and replaces text by regular expression. Configure it once for the whole Wordpress Multisite installation and the replacements will be made in every post network-wide.
(Sites cannot change the settings - only network admins can).

## Settings

### Replacements

Write the replacements as a JSON object.
```
{
    "/My name is (\\w+)/i": "Hi $1",
    "/(bold)/i": "<strong>$1<\/strong>"
}
```

#### Input
My name is Anthony

bold


#### Output
Hi Anthony

**bold**
