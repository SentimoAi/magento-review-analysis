# Sentimo Review Analysis Module for Magento 2
The Sentimo Review Analysis module integrates your Magento 2 store with [Sentimo](https://sentimoai.com), an advanced platform providing sentiment analysis of customer reviews. By leveraging Sentimo's AI-driven insights, this module automates the process of analyzing review sentiments and updating review statuses based on sentiment scores, directly within your Magento backend.

## Installation
To install the Sentimo Review Analysis module in your Magento 2 store, execute the following commands from the root directory of your Magento installation:

```bash
    composer require sentimo/review-analysis
    bin/magento setup:upgrade
    bin/magento cache:clean
```

## Configuration
After installing the module, you can configure it from the Magento Admin Panel by navigating to:

Stores > Configuration > Sentimo > Review Analysis

### Sync Settings
Configure the synchronization settings between your Magento store and the Sentimo API.

- Enabled: Turn the module's functionality on or off.
- Api Base Uri: Set the base URI for the Sentimo API.
- Api Key: Provide your Sentimo API key. This field is encrypted for security.
- Channel: Specify the channel or source of the reviews within your Sentimo account.
- Post Review Frequency: Define how often reviews should be posted to Sentimo for analysis. Enter a cron expression.
- Sync Review Frequency: Define how often reviews should be synced from Sentimo based on analysis results. Enter a cron expression.

### Date Range for Review Sync
Set the date range for which reviews will be considered for synchronization with Sentimo.

- From Date: Select the start date from which reviews will be included in the sync.
- To Date: Select the end date until which reviews will be considered for the sync.

## Usage
After configuring the module, it will automatically sync and analyze new customer reviews based on the specified frequencies. Ensure that your cron jobs are correctly configured in Magento to trigger the module's scheduled tasks.

## Support and Contact

For support, feature requests, or any questions about the Sentimo Review Analysis Module, please reach out to us:

- **Email**: contact@sentimoai.com
- **Contact Us on Sentimo**: [Contact Us](https://sentimoai.com/contact/index)

We're here to help make your experience with our module as smooth and beneficial as possible. Feedback and suggestions are always welcome!
